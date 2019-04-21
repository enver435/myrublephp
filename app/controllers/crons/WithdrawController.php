<?php

    namespace App\Controllers\Crons;
    use App\Models\Crons\UserModel;
    use App\Models\Crons\WithdrawModel;
    use App\Controllers\BaseController;
    use App\System\Libraries\Firebase;
    use App\System\Libraries\CPayeer;
    use \YandexMoney\API;

    class WithdrawController extends BaseController
    {
        private $json = [];

        public function withdrawYandex($request, $response, $args)
        {
            // $scope = ['account-info', 'operation-history', 'operation-details', 'payment-p2p'];
            // $auth_url = API::buildObtainTokenUrl(getenv('YANDEX_CLIENT_ID'), getenv('YANDEX_REDIRECT_URI'), $scope);
            // echo $auth_url;

            // $access_token_response = API::getAccessToken(getenv('YANDEX_CLIENT_ID'), getenv('YANDEX_CODE'), getenv('YANDEX_REDIRECT_URI'), null);
            // $access_token = $access_token_response->access_token;
            // echo $access_token;
            
            try {
                // get payment method information
                $methodInfo = WithdrawModel::methodInfo(1);
                
                if($methodInfo->auto_payment == 1) {
                    // init firebase
                    $firebase = Firebase::init();
    
                    // api set access token
                    $api = new API(getenv('YANDEX_ACCESS_TOKEN'));
                    
                    // get waiting withdraws
                    $withdraws = WithdrawModel::withdraws([
                        ['payment_method', '=', 1],
                        ['payment_status', '=', 0]
                    ], 10);
    
                    if(count($withdraws) > 0) {
                        foreach ($withdraws as $withdraw) {
                            // get user information
                            $userInfo = UserModel::info(
                                ['id' => $withdraw->user_id],
                                ['firebase_token']
                            );
    
                            // make request payment
                            $request_payment = $api->requestPayment([
                                'pattern_id' => 'p2p',
                                'to' => $withdraw->wallet_number,
                                'amount_due' => $withdraw->amount,
                                'message' => 'myRuble: Перевод #' . $withdraw->id . '. Можете дать по рейтингу Google Play?',
                                'comment' => 'myRuble: Перевод #' . $withdraw->id,
                                'label' => 'myRuble: Перевод #' . $withdraw->id
                            ]);
    
                            if($request_payment->status == 'success') {
                                if($request_payment->balance >= $request_payment->contract_amount) {
                                    // call process payment to finish payment
                                    $process_payment = $api->processPayment([
                                        'request_id' => $request_payment->request_id
                                    ]);
    
                                    if($process_payment->status == 'success') {
                                        // update withdraw
                                        WithdrawModel::update(['id' => $withdraw->id], [
                                            'payment_id' => $process_payment->payment_id,
                                            'payment_status' => 1
                                        ]);
    
                                        // send notification
                                        $title   = 'Ваш платеж успешен';
                                        $message = $withdraw->amount . ' рублей было отправлено на ваш счет ' . $withdraw->wallet_number;
                                        $firebase->sendNotify($userInfo->firebase_token, $title, $message);
                                    }
                                }
                            } elseif (
                                $request_payment->status == 'refused' && 
                                ($request_payment->error == 'illegal_param_to' || $request_payment->error == 'payee_not_found')
                            ) {
                                // update withdraw
                                WithdrawModel::update(['id' => $withdraw->id], [
                                    'payment_status' => 2,
                                    'not_paid_type' => 1 // wallet incorrect
                                ]);
    
                                // update user balance
                                UserModel::update(['id' => $withdraw->user_id], [
                                    'balance' => $this->db->raw('balance + ' . round(($withdraw->amount + ($withdraw->amount * $withdraw->commission / 100)), 2))
                                ]);
    
                                // send notification
                                $title   = 'Ваш платеж не успешен';
                                $message = 'Ваш ' . $withdraw->wallet_number . ' кошелек неверен. Пожалуйста, проверьте другой кошелек';
                                $firebase->sendNotify($userInfo->firebase_token, $title, $message);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // set json data
                $this->json = [
                    'status'  => false,
                    'message' => $e->getMessage()
                ];
            }

            // return response json data
            // return $response->withJson($this->json);
        }

        public function withdrawPayeer($request, $response, $args)
        {
            try {
                // get payment method information
                $methodInfo = WithdrawModel::methodInfo(2);

                if($methodInfo->auto_payment == 1) {
                    // init firebase
                    $firebase = Firebase::init();
        
                    // payeer init
                    $payeer = new CPayeer();
        
                    if ($payeer->isAuth()) {
                        // get payeer balance
                        $getBalance = $payeer->getBalance();
                        $balance = (float) $getBalance['balance']['RUB']['BUDGET'];
        
                        // get waiting withdraws
                        $withdraws = WithdrawModel::withdraws([
                            ['payment_method', '=', 2],
                            ['payment_status', '=', 0]
                        ], 10);
                        
                        if(count($withdraws) > 0) {
                            foreach ($withdraws as $withdraw) {
                                $comissionBalance = round(($withdraw->amount + ($withdraw->amount * 0.95 / 100)), 2);
                                if($balance >= $comissionBalance) {
                                    // get user information
                                    $userInfo = UserModel::info(
                                        ['id' => $withdraw->user_id],
                                        ['firebase_token']
                                    );
                                    
                                    // check wallet
                                    $checkWallet = $payeer->checkUser([
                                        'user' => $withdraw->wallet_number
                                    ]);
                                    if($checkWallet) {
                                        $arTransfer = $payeer->transfer([
                                            'curIn' => 'RUB',
                                            'sum' => $comissionBalance, // komissiya ile birlikde (tam mebleg gondermek ucun)
                                            'curOut' => 'RUB',
                                            'to' => $withdraw->wallet_number,
                                            'comment' => 'myRuble: Перевод #' . $withdraw->id . '. Можете дать по рейтингу Google Play?'
                                        ]);
                                        if (empty($arTransfer['errors'])) {
                                            // update withdraw
                                            WithdrawModel::update(['id' => $withdraw->id], [
                                                'payment_id' => $arTransfer['historyId'],
                                                'payment_status' => 1
                                            ]);
        
                                            // payeer balance decrement
                                            $balance -= $comissionBalance;
        
                                            // send notification
                                            $title   = 'Ваш платеж успешен';
                                            $message = $withdraw->amount . ' рублей было отправлено на ваш счет ' . $withdraw->wallet_number;
                                            $firebase->sendNotify($userInfo->firebase_token, $title, $message);
                                        }
                                    } else {
                                        // update withdraw
                                        WithdrawModel::update(['id' => $withdraw->id], [
                                            'payment_status' => 2,
                                            'not_paid_type' => 1 // wallet incorrect
                                        ]);
        
                                        // update user balance
                                        UserModel::update(['id' => $withdraw->user_id], [
                                            'balance' => $this->db->raw('balance + ' . round(($withdraw->amount + ($withdraw->amount * $withdraw->commission / 100)), 2))
                                        ]);
        
                                        // send notification
                                        $title   = 'Ваш платеж не успешен';
                                        $message = 'Ваш ' . $withdraw->wallet_number . ' кошелек неверен. Пожалуйста, проверьте другой кошелек';
                                        $firebase->sendNotify($userInfo->firebase_token, $title, $message);
                                    }
                                }
                            }
                        }
                    } else {
                        throw new \Exception('Payeer Auth Error', 401);
                    }
                }
            } catch (\Exception $e) {
                // set json data
                $this->json = [
                    'status'  => false,
                    'message' => $e->getMessage()
                ];
            }

            // return response json data
            return $response->withJson($this->json);
        }
    } 

?>