<?php

    namespace App\Controllers\Crons;
    use App\Models\Crons\UserModel;
    use App\Models\Crons\WithdrawModel;
    use App\Controllers\BaseController;
    use App\System\Libraries\Firebase;
    use App\System\Libraries\CPayeer;

    class WithdrawController extends BaseController
    {
        private $json = [];

        public function withdrawPayeer($request, $response, $args)
        {
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
                            if($userInfo !== false) {
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
                }
            } else {
                // set json data
                $this->json = [
                    'status'  => false,
                    'message' => 'Payeer Auth Error'
                ];
            }

            // return response json data
            return $response->withJson($this->json);
        }
    } 

?>