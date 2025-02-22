<?php

    namespace App\Controllers\Api;
    use App\Models\Api\WithdrawModel;
    use App\Controllers\BaseController;
    use App\Models\Dashboard\UserModel;

    class WithdrawController extends BaseController
    {
        private $json = [];

        /**
         * Get Withdraws
         */
        public function withdraws($request, $response, $args)
        {
            // get query params
            $params = $request->getQueryParams();

            $where = null;
            if(!empty($params)) {
                foreach ($params as $key => $value) {
                    if($key != 'limit' && $key != 'offset' && isset($value) && $value != '') {
                        // set where
                        $where[] = [$key, '=', $value];
                    }
                }
            }

            try {
                // set json data
                $this->json = [
                    'status' => true,
                    'data'   => WithdrawModel::withdraws($where, @$params['limit'], @$params['offset'])
                ];
            } catch (\Illuminate\Database\QueryException $e) {
                // set json data
                $this->json = [
                    'status'  => false,
                    'message' => 'Database Error: ' . $e->getMessage()
                ];
            }

            // return response json data
            return $response->withJson($this->json);
        }

        /**
         * Insert Withdraw
         */
        public function insertWithdraw($request, $response, $args)
        {
            $body = $request->getParsedBody();

            $user_id = $body['user_id'];
            $method = $body['method'];
            $amount = round($body['amount'], 2);
            $wallet_number = strtoupper(strip_tags(trim($body['wallet_number'])));

            if($user_id > 0 && $method > 0 && $amount > 0 && strlen($wallet_number) > 0) {

                // if webmoney and wallet incorrect
                if($method == 3 && !preg_match('#^R[0-9]{12}$#i', $wallet_number)) {
                    // return response json data
                    return $response->withJson([
                        'status' => false,
                        'message' => $this->trans('api/withdraw.wallet_incorrect')
                    ]);
                }

                try {
                    // get payment method information
                    $methodInfo = WithdrawModel::methodInfo($method);
    
                    // get user information
                    $userInfo = UserModel::info(['id' => $user_id]);
    
                    if($userInfo !== false && $methodInfo !== false) {

                        if($userInfo->ban == 0) {
                            if($amount >= $methodInfo->min_withdraw) {
                                // calculate commission balance
                                $commissionBalance = round($amount + ($amount * $methodInfo->commission / 100), 2);
                                if($userInfo->balance >= $commissionBalance) {
                                    $lastID = WithdrawModel::insert([
                                        'user_id' => $user_id,
                                        'amount' => $amount,
                                        'commission' => $methodInfo->commission,
                                        'payment_method' => $method,
                                        'wallet_number' => $wallet_number,
                                        'time' => time()
                                    ]);
            
                                    if($lastID > 0) {
                                        // update user for me
                                        UserModel::update(['id' => $user_id], [
                                            'balance' => $this->db->raw('balance - ' . $commissionBalance),
                                        ]);
            
                                        // set json data
                                        $this->json = [
                                            'status' => true,
                                            'data' => UserModel::infoFull(['users.id' => $user_id]),
                                            'message' => $this->trans('api/withdraw.withdraw_success')
                                        ];
                                    }
                                } else {
                                    // set json data
                                    $this->json = [
                                        'status' => false,
                                        'message' => $this->trans('api/withdraw.balance_error')
                                    ];
                                }
                            } else {
                                // set json data
                                $this->json = [
                                    'status' => false,
                                    'message' => $this->trans('api/withdraw.min_withdraw', ['amount' => round($methodInfo->min_withdraw, 2)])
                                ];
                            }
                        } else {
                            // set json data
                            $this->json = [
                                'status' => false,
                                'message' => $this->trans('api/user.banned')
                            ];
                        }
                                             
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                    // set json data
                    $this->json = [
                        'status'  => false,
                        'message' => 'Database Error: ' . $e->getMessage()
                    ];
                }
            } else {
                // set json data
                $this->json = [
                    'status'  => false,
                    'message' => $this->trans('api/withdraw.empty')
                ];
            }

            // return response json data
            return $response->withJson($this->json);
        }

        /**
         * Payment Methods
         */
        public function paymentMethods($request, $response, $args)
        {
            try {
                // set json data
                $this->json = [
                    'status' => true,
                    'data'   => WithdrawModel::paymentMethods()
                ];
            } catch (\Illuminate\Database\QueryException $e) {
                // set json data
                $this->json = [
                    'status'  => false,
                    'message' => 'Database Error: ' . $e->getMessage()
                ];
            }

            // return response json data
            return $response->withJson($this->json);
        }
    }

?>