<?php

    namespace App\Controllers\Site;
    use App\Controllers\BaseController;
    use App\Models\Site\WithdrawModel;
    use App\Models\BaseModel;
    use App\System\Helpers\Email;
    use App\System\Helpers\Url;
    use App\Models\Site\UserModel;
    use App\Models\Site\ReferralModel;
    use App\System\Helpers\Main;
    use App\System\Helpers\Cookie;

    class MainController extends BaseController
    {
        public function index($request, $response, $args)
        {
            // render page
            $withdraws = WithdrawModel::withdraws(['payment_status' => 1], 5);
            $totalSumWithdraw = BaseModel::sum('withdraws', 'amount', ['payment_status' => 1]);
            return $this->view->render($response, 'site/index.html', [
                'flash' => $this->flash->getMessages(),
                'totalSumWithdraw' => $totalSumWithdraw,
                'withdraws' => $withdraws,
                'ref_code' => @$args['ref_code']
            ]);
        }

        public function register($request, $response, $args)
        {
            if($request->isPost()) {
                // get parsed body
                $body = $request->getParsedBody();

                // init google recaptcha
                $recaptcha = new \ReCaptcha\ReCaptcha(getenv('RECAPTCHA_SECRET'));
                $resp = $recaptcha->verify($body['g-recaptcha-response'], Main::getIp());

                // if google recaptcha success
                if ($resp->isSuccess()) {
                    // post body get data
                    $email    = mb_strtolower(trim($body['email']), 'UTF-8');
                    $username = mb_strtolower(trim($body['username']), 'UTF-8');
                    $pass     = strip_tags(trim($body['pass']));
                    $ref_code = strip_tags(trim($body['ref_code']));
                    $guid     = strip_tags(trim($body['guid']));

                    // validate body
                    $validate = false;
                    if(!Cookie::get('guid') && !UserModel::exist(['ip_address' => Main::getIp()])) {
                        if($email != '' && $username != '' && $pass != '') {
                            if(Email::valid($email)) {
                                if(preg_match('/^[a-z0-9_-]{3,15}$/i', $username)) {
                                    if(strlen($pass) >= 6) {
                                        $validate = true;
                                    } else {
                                        // add flash message
                                        $this->flash->addMessage('danger', 'Пароль должен содержать не менее 6 символов');
                                    }
                                } else {
                                    // add flash message
                                    $this->flash->addMessage('danger', 'Неверное имя пользователя');
                                }
                            } else {
                                // add flash message
                                $this->flash->addMessage('danger', 'Неверный электронной почты');
                            }
                        } else {
                            // add flash message
                            $this->flash->addMessage('danger', 'Пожалуйста, не оставляйте пустые строки пустыми');
                        }
                    } else {
                        // add flash message
                        $this->flash->addMessage('danger', 'Вы можете зарегистрироваться один раз');
                    }

                    // if validation status true
                    if($validate) {
                        try {
                            $existEmail    = UserModel::exist(['email' => $email]);
                            $existUsername = UserModel::exist(['username' => $username]);

                            // database insert status
                            $insert = true;

                            if($existEmail) {
                                // add flash message
                                $this->flash->addMessage('danger', 'Этот адрес электронной почты уже используется');

                                // set insert status
                                $insert = false;
                            } elseif($existUsername) {
                                // add flash message
                                $this->flash->addMessage('danger', 'Имя пользователя уже используется');

                                // set insert status
                                $insert = false;
                            } elseif($ref_code != '') {
                                $refUserInfo = UserModel::info(['referral_code' => $ref_code], ['id']);
                                if($refUserInfo === false || $refUserInfo->ban == 1) {
                                    // add flash message
                                    $this->flash->addMessage('danger', 'Код реферала не найден');
                                    
                                    // set insert status
                                    $insert = false;
                                }
                            }

                            if($insert) {
                                // insert user
                                $lastId = UserModel::insert([
                                    'email'          => $email,
                                    'username'       => $username,
                                    'pass'           => md5($pass),
                                    'register_time'  => time(),
                                    'last_seen_time' => time(),
                                    'referrer'       => 2, // site
                                    'ip_address'     => Main::getIp()
                                ]);
                                if($lastId > 0) {
                                    // update referral code
                                    UserModel::update(['id' => $lastId], [
                                        'referral_code' => str_pad($lastId, 6, '0', STR_PAD_LEFT)
                                    ]);

                                    // insert referral
                                    if($ref_code != '') {
                                        ReferralModel::insert([
                                            'user_id'     => $lastId,
                                            'ref_user_id' => $refUserInfo->id,
                                            'time'        => time()
                                        ]);
                                    }

                                    // set cookie guid 365 day
                                    Cookie::set('guid', strval($guid), (10 * 365 * 24 * 60 * 60));
                                }
                            }
                        } catch (\Illuminate\Database\QueryException $e) {
                            // add flash message
                            $this->flash->addMessage('danger', 'Database Error: ' . $e->getMessage());
                        }
                    }
                } else {
                    // add flash message
                    $this->flash->addMessage('danger', 'ReCaptcha неправильно');
                }

                if(!$insert) {
                    return Url::redirect('register', ['ref_code' => @$args['ref_code']]);
                }
            }

            // render page
            $withdraws = WithdrawModel::withdraws(['payment_status' => 1], 5);
            $totalSumWithdraw = BaseModel::sum('withdraws', 'amount', ['payment_status' => 1]);
            return $this->view->render($response, 'site/register.html', [
                'flash' => $this->flash->getMessages(),
                'totalSumWithdraw' => $totalSumWithdraw,
                'withdraws' => $withdraws,
                'ref_code' => @$args['ref_code']
            ]);
        }

        public function privacy($request, $response, $args)
        {
            return $this->view->render($response, 'site/privacy.html', [
                'appName' => @$args['app'] == 'incoin' ? 'INcoin' : 'myRuble'
            ]);
        }
    }

?>