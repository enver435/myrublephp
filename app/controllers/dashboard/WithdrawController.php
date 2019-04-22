<?php

    namespace App\Controllers\Dashboard;
    use App\Controllers\BaseController;
    use App\Models\BaseModel;
    use App\Models\Dashboard\WithdrawModel;
    use App\Models\Dashboard\UserModel;
    use App\System\Helpers\Url;
    use App\System\Libraries\Firebase;
    use App\System\Libraries\Pagination;

    class WithdrawController extends BaseController
    {
        public function index($request, $response, $args)
        {
            // get query params
            $params = $request->getQueryParams();

            $where = null;

            // if exist status param
            if(isset($params['status']) && $params['status'] >= 0) {
                // set where
                $where[] = ['payment_status', '=', $params['status']];
            }

            // if exist user_id param
            if(isset($params['user_id']) && $params['user_id'] > 0) {
                // set where
                $where[] = ['user_id', '=', $params['user_id']];
            }

            // get total items
            $totalItems = BaseModel::count('withdraws', $where);

            // init pagination
            $perPage     = 10;

            $urlPattern  = Url::pathFor('dashboard.withdraws') . 
            (isset($params['status']) && $params['status'] >= 0 ? '?status=' . $params['status'] : null) .
            (isset($params['user_id']) && $params['user_id'] >= 0 ? '?user_id=' . $params['user_id'] : null);

            $pagination = Pagination::init($totalItems, @$params['page'], $perPage, $urlPattern);
            
            // get withdraws
            $withdraws = WithdrawModel::withdraws($where, $pagination->limit(), $pagination->offset());

            // render page
            return $this->view->render($response, 'dashboard/withdraws/index.html', [
                'pagination' => $pagination->links(),
                'withdraws'  => $withdraws
            ]);
        }

        public function edit($request, $response, $args)
        {
            $id = $args['id'];
            if($id > 0) {
                if($request->isPost()) {
                    // get post body
                    $body = $request->getParsedBody();

                    // update status
                    $update = true;

                    if($body['wallet_number'] == '') {
                        $update = false;
                    } elseif($body['payment_status'] == 2 && $body['not_paid_selectbox'] == 0) {
                        $update = false;
                    }

                    if($update === true) {
                        try {
                            // init firebase
                            $firebase = Firebase::init();

                            // update information
                            WithdrawModel::update(['id' => $id], [
                                'wallet_number'  => $body['wallet_number'],
                                'payment_status' => $body['payment_status'],
                                'not_paid_type'  => $body['not_paid_selectbox']
                            ]);

                            // get withdraw information
                            $info = WithdrawModel::info(['id' => $id]);

                            // get user information
                            $userInfo = UserModel::info(['id' => $info->user_id]);
                            
                            // eger odenilibse
                            if($body['payment_status'] == 1) {
                                // send notification
                                $title   = 'Ваш платеж успешен';
                                $message = $info->amount . ' рублей было отправлено на ваш счет ' . $info->wallet_number;
                                $firebase->sendNotify($userInfo->firebase_token, $title, $message);
                            }
                            // eger odenilmeyibse
                            elseif($body['payment_status'] == 2) {
                                // update user balance
                                UserModel::update(['id' => $userInfo->id], [
                                    'balance' => $this->db->raw('balance + ' . round(($info->amount + ($info->amount * $info->commission / 100)), 2))
                                ]);

                                // send notification
                                $title = 'Ваш платеж не успешен';

                                /**
                                 * 1 => wallet incorrect
                                 */
                                if($body['not_paid_selectbox'] == 1) {
                                    $message = 'Ваш ' . $body['wallet_number'] . ' кошелек неверен. Пожалуйста, проверьте другой кошелек';
                                } else {
                                    $message = '';
                                }
                                $firebase->sendNotify($userInfo->firebase_token, $title, $message);
                            }

                            // add flash message
                            $this->flash->addMessage('success', 'Məlumatlar yeniləndi');
                        } catch (\Exception $e) {
                            // add flash message
                            $this->flash->addMessage('danger', 'Error: ' . $e->getMessage());
                        }
                    } else {
                        // add flash message
                        $this->flash->addMessage('danger', 'Zəhmət olmasa xanaları doldurun');
                    }
                    // redirect page
                    return Url::redirect('dashboard.withdraws.edit', ['id' => $id]);
                }
                
                // get information
                $info = WithdrawModel::info(['id' => $id]);
                if($info !== false && $info->payment_status == 0) {
                    // render page
                    return $this->view->render($response, 'dashboard/withdraws/edit.html', [
                        'flash' => $this->flash->getMessages(),
                        'item'  => $info
                    ]);
                }
            }
            // redirect page
            return Url::redirect('dashboard.withdraws');
        }
    }

?>