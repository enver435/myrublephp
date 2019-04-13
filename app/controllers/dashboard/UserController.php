<?php

    namespace App\Controllers\Dashboard;

    use App\Controllers\BaseController;
    use App\Models\BaseModel;
    use App\Models\Dashboard\UserModel;
    use App\System\Helpers\Url;
    use App\System\Libraries\Pagination;

    class UserController extends BaseController
    {
        public function index($request, $response, $args)
        {
            // get query params
            $params = $request->getQueryParams();

            // get total items
            $totalItems = BaseModel::count('users');

            // init pagination
            $perPage     = 10;
            $urlPattern  = Url::pathFor('dashboard.users');
            $pagination  = Pagination::init($totalItems, @$params['page'], $perPage, $urlPattern);
            
            // get users
            $users = UserModel::users(null, $pagination->offset(), $pagination->limit());

            // render page
            return $this->view->render($response, 'dashboard/users/index.html', [
                'pagination' => $pagination->links(),
                'users'      => $users
            ]);
        }

        public function edit($request, $response, $args)
        {
            $id = $args['id'];
            if($id > 0) {
                if($request->isPost()) {
                    // get post body
                    $body = $request->getParsedBody();

                    // post body get data
                    $email    = filter_var(mb_strtolower($body['email'], 'UTF-8'), FILTER_SANITIZE_EMAIL);
                    $username = mb_strtolower($body['username'], 'UTF-8');
                    $heart    = $body['heart'];
                    $balance  = $body['balance'];
                    $referrer = $body['referrer'];

                    $validate = false;

                    // validate body
                    if($email != '' && $username != '') {
                        if(filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
                            if(preg_match('/^[a-z0-9_-]{3,15}$/i', $username)) {
                                $validate = true;
                            }
                        }
                    }

                    // if validation status true
                    if($validate === true) {
                        try {
                            // database update status
                            $update = true;

                            // get information
                            $info = UserModel::info(['id' => $id]);
                            
                            if($info->email != $email || $info->username != $username) {
                                $existEmail    = UserModel::exist(['email' => $email]);
                                $existUsername = UserModel::exist(['username' => $username]);
    
                                if($existEmail === true) {
                                    // add flash message
                                    $this->flash->addMessage('danger', 'Bu email artıq istifadə olunur. Zəhmət olmasa başqa email yoxlayın');
                                    
                                    // set update status
                                    $update = false;
                                } elseif($existUsername === true) {
                                    // add flash message
                                    $this->flash->addMessage('danger', 'Bu istifadəçi adı artıq istifadə olunur. Zəhmət olmasa başqa istifadəçi adı yoxlayın');
                                    
                                    // set update status
                                    $update = false;
                                }
                            }

                            if($update === true) {
                                // update user
                                UserModel::update(['id' => $id], [
                                    'username' => $username,
                                    'email'    => $email,
                                    'heart'    => $heart,
                                    'balance'  => $balance,
                                    'referrer' => $referrer
                                ]);
                                
                                // add flash message
                                $this->flash->addMessage('success', 'Məlumatlar yeniləndi');
                            }
                        } catch (\Illuminate\Database\QueryException $e) {
                            // add flash message
                            $this->flash->addMessage('danger', 'Database Error: ' . $e->getMessage());
                        }
                    } else {
                        // add flash message
                        $this->flash->addMessage('danger'. 'Zəhmət olmasa xanaları düzgün doldurun');
                    }
                    // redirect page
                    return Url::redirect('dashboard.users.edit', ['id' => $id]);
                }

                // get information
                $info = UserModel::info(['id' => $id]);
                if($info !== false) {
                    // render page
                    return $this->view->render($response, 'dashboard/users/edit.html', [
                        'flash' => $this->flash->getMessages(),
                        'item'  => $info
                    ]);
                }
            }
            // redirect page
            return Url::redirect('dashboard.users');
        }

        public function show($request, $response, $args)
        {
            $id = $args['id'];
            if($id > 0) {
                // get information
                $info = UserModel::infoFull(['users.id' => $id]);
                if($info !== false) {

                    $dayStart = strtotime(date('d.m.Y') . ' 00:00');
                    $dayEnd   = strtotime(date('d.m.Y') . ' 23:59');

                    /**
                     * Total Analytics
                     */
                    $renderData['totalGameCount']    = BaseModel::count('game_logs', ['user_id' => $id]);
                    $renderData['totalWinGameCount'] = BaseModel::count('game_logs', [
                        ['user_id', '=', $id],
                        ['status', '=', 1]
                    ]);
                    $renderData['totalLoseGameCount'] = BaseModel::count('game_logs', [
                        ['user_id', '=', $id],
                        ['status', '=', 0]
                    ]);
                    $renderData['totalSumEarn'] = BaseModel::sum('game_logs', 'earn', [
                        ['user_id', '=', $id],
                        ['status', '=', 1]
                    ]);
                    $renderData['totalSumWithdraw'] = BaseModel::sum('withdraws', 'amount', [
                        ['user_id', '=', $id],
                        ['payment_status', '=', 1]
                    ]);
                    
                    /**
                     * Today Analytics
                     */
                    $renderData['todayGameCount'] = BaseModel::count('game_logs', [
                        ['user_id', '=', $id],
                        ['time', '>=', $dayStart],
                        ['time', '<=', $dayEnd]
                    ]);
                    $renderData['todayWinGameCount'] = BaseModel::count('game_logs', [
                        ['user_id', '=', $id],
                        ['time', '>=', $dayStart],
                        ['time', '<=', $dayEnd],
                        ['status', '=', 1]
                    ]);
                    $renderData['todayLoseGameCount'] = BaseModel::count('game_logs', [
                        ['user_id', '=', $id],
                        ['time', '>=', $dayStart],
                        ['time', '<=', $dayEnd],
                        ['status', '=', 0]
                    ]);
                    $renderData['todaySumEarn'] = BaseModel::sum('game_logs', 'earn', [
                        ['user_id', '=', $id],
                        ['time', '>=', $dayStart],
                        ['time', '<=', $dayEnd],
                        ['status', '=', 1]
                    ]);
                    $renderData['todaySumWithdraw'] = BaseModel::sum('withdraws', 'amount', [
                        ['user_id', '=', $id],
                        ['time', '>=', $dayStart],
                        ['time', '<=', $dayEnd],
                        ['payment_status', '=', 1]
                    ]);

                    // set render data user information
                    $renderData['info'] = $info;

                    // render page
                    return $this->view->render($response, 'dashboard/users/show.html', $renderData);
                }
            }
            // redirect page
            return Url::redirect('dashboard.users');
        }
    }

?>