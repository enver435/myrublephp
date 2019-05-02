<?php

    namespace App\Controllers\Crons;
    use App\Models\Crons\UserModel;
    use App\Models\Crons\WithdrawModel;
    use App\Controllers\BaseController;
    use App\System\Libraries\Firebase;
    use App\System\Libraries\CPayeer;
    use \YandexMoney\API;
    use \baibaratsky\WebMoney\WebMoney;
    use \baibaratsky\WebMoney\Signer;
    use \baibaratsky\WebMoney\Request\Requester\CurlRequester;
    use \baibaratsky\WebMoney\Api\X\X2;

    class WithdrawController extends BaseController
    {
        private $json = [];

        public function withdrawYandex($request, $response, $args)
        {            
            try {
                // get payment method information
                $methodInfo = WithdrawModel::methodInfo(1);

                if($methodInfo->auto_payment == 1) {
                    // init firebase
                    $firebase = Firebase::init();
    
                    // api set access token
                    $api = new API(getenv('YANDEX_ACCESS_TOKEN'));
                    
                    // get waiting withdraw
                    $withdraw = WithdrawModel::info([
                        ['payment_method', '=', 1],
                        ['payment_status', '=', 0]
                    ]);

                    if(!empty($withdraw)) {
                        // get user information
                        $userInfo = UserModel::info(['id' => $withdraw->user_id]);
                        
                        if($userInfo->ban == 1) {
                            // update withdraw
                            WithdrawModel::update(['id' => $withdraw->id], [
                                'payment_status' => 2,
                                'not_paid_type' => 2 // user banned
                            ]);
                        } else {
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
                                // call process payment to finish payment
                                $process_payment = $api->processPayment([
                                    'request_id' => $request_payment->request_id
                                ]);
    
                                if($process_payment->status == 'success') {
                                    // update withdraw
                                    WithdrawModel::update(['id' => $withdraw->id], [
                                        'payment_id' => $process_payment->payment_id,
                                        'payment_status' => 1,
                                        'payment_time' => time()
                                    ]);
    
                                    // send notification
                                    $title   = 'Ваш платеж успешен';
                                    $message = $withdraw->amount . ' рублей было отправлено на ваш счет ' . $withdraw->wallet_number;
                                    $firebase->sendNotify($userInfo->firebase_token, $title, $message);
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
            return $response->withJson($this->json);
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
                        // get waiting withdraw
                        $withdraw = WithdrawModel::info([
                            ['payment_method', '=', 2],
                            ['payment_status', '=', 0]
                        ]);
                        
                        if(!empty($withdraw)) {
                            // calculate commission balance
                            $comissionBalance = round(($withdraw->amount + ($withdraw->amount * 0.95 / 100)), 2);

                            // get user information
                            $userInfo = UserModel::info(['id' => $withdraw->user_id]);
                            
                            if($userInfo->ban == 1) {
                                // update withdraw
                                WithdrawModel::update(['id' => $withdraw->id], [
                                    'payment_status' => 2,
                                    'not_paid_type' => 2 // user banned
                                ]);
                            } else {
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
                                            'payment_status' => 1,
                                            'payment_time' => time()
                                        ]);
    
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

        public function withdrawWebmoney($request, $response, $args)
        {
            try {
                // get payment method information
                $methodInfo = WithdrawModel::methodInfo(3);

                if($methodInfo->auto_payment == 1) {
                    // init firebase
                    $firebase = Firebase::init();
                    
                    // get waiting withdraw
                    $withdraw = WithdrawModel::info([
                        ['payment_method', '=', 3],
                        ['payment_status', '=', 0]
                    ]);

                    if(!empty($withdraw)) {
                        // get user information
                        $userInfo = UserModel::info(['id' => $withdraw->user_id]);
                        
                        if($userInfo->ban == 1) {
                            // update withdraw
                            WithdrawModel::update(['id' => $withdraw->id], [
                                'payment_status' => 2,
                                'not_paid_type' => 2 // user banned
                            ]);
                        } else {
                            // init webmoney
                            $webMoney = new WebMoney(new CurlRequester);

                            // make request payment
                            $wrequest = new X2\Request;
                            $wrequest->setSignerWmid(getenv('WEBMONEY_WMID'));
                            $wrequest->setTransactionExternalId($withdraw->id); // Unique ID of the transaction in your system
                            $wrequest->setPayerPurse(getenv('WEBMONEY_WMR'));
                            $wrequest->setPayeePurse($withdraw->wallet_number);
                            $wrequest->setAmount($withdraw->amount); // Payment amount
                            $wrequest->setDescription('myRuble: Перевод #' . $withdraw->id . '. Можете дать по рейтингу Google Play?');
                            $wrequest->setOnlyAuth(false);

                            // auth webmoney
                            $wrequest->sign(new Signer(getenv('WEBMONEY_WMID'), getenv('WEBMONEY_WMID') . '.kwm', getenv('WEBMONEY_PASS')));

                            // if valid request 
                            if ($wrequest->validate()) {
                                /** @var X2\Response $wresponse */
                                $wresponse = $webMoney->request($wrequest);

                                // get response code
                                $resCode = $wresponse->getReturnCode();

                                if ($resCode == 0) {
                                    // update withdraw
                                    WithdrawModel::update(['id' => $withdraw->id], [
                                        'payment_id' => $wresponse->getTransactionId(),
                                        'payment_status' => 1,
                                        'payment_time' => time()
                                    ]);
    
                                    // send notification
                                    $title   = 'Ваш платеж успешен';
                                    $message = $withdraw->amount . ' рублей было отправлено на ваш счет ' . $withdraw->wallet_number;
                                    $firebase->sendNotify($userInfo->firebase_token, $title, $message);
                                } elseif($resCode == -5 || $resCode == 7 || $resCode == 29) {
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