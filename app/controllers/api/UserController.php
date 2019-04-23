<?php

    namespace App\Controllers\Api;
    use App\Models\Api\UserModel;
    use App\Controllers\BaseController;
    use App\Models\Api\ReferralModel;

    class UserController extends BaseController
    {
        private $json     = [];
        private $validate = false;

        /**
         * Get User Information
         */
        public function userInfo($request, $response, $args)
        {
            $params = $request->getQueryParams();

            // set array where data
            $where = [];
            foreach ($params as $key => $value) {
                if($key != 'full') {
                    $where[] = [(@$params['full'] == "true" ? "users.$key" : $key), '=', $value];
                }
            }

            if(count($where) > 0) {
                try {
                    // user information
                    $userInfo = (@$params['full'] == "true" ? UserModel::infoFull($where) : UserModel::info($where));
                    if($userInfo !== false) {
                        // set json data
                        $this->json = [
                            'status' => true,
                            'data'   => $userInfo
                        ];
                    } else {
                        // set json data
                        $this->json = [
                            'status'  => false,
                            'message' => 'Пользователь не найден'
                        ];
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
                    'message' => 'Пользователь не найден'
                ];
            }

            // return response json data
            return $response->withJson($this->json);
        }

        /**
         * Update User
         */
        public function updateUser($request, $response, $args)
        {
            $body = $request->getParsedBody();

            if($body['id'] > 0 && count($body['data']) > 0) {
                
                // set array update data
                $updateData = [];
                foreach ($body['data'] as $key => $value) {
                    if(
                        isset($body['data'][$key]['increment']) &&
                        $body['data'][$key]['increment'] == "true"
                    ) {
                        $updateData[$key] = $this->db->raw($key . ' + ' . $body['data'][$key]['value']);
                    } elseif(
                        isset($body['data'][$key]['decrement']) &&
                        $body['data'][$key]['decrement'] == "true"
                    ) {
                        $updateData[$key] = $this->db->raw($key . ' - ' . $body['data'][$key]['value']);
                    } elseif(
                        isset($body['data'][$key]['currentTime']) &&
                        $body['data'][$key]['currentTime'] == "true"
                    ) {
                        $updateData[$key] = time();
                    } elseif(
                        isset($body['data'][$key]['strToTime']) &&
                        $body['data'][$key]['strToTime'] == "true"
                    ) {
                        $updateData[$key] = strtotime($body['data'][$key]['value'], time());
                    } else {
                        $updateData[$key] = strip_tags(trim($value));
                    }
                }

                try {
                    $where = ['id' => $body['id']];

                    // check exist user
                    $exist = UserModel::exist($where);
                    if($exist) {
                        // update user
                        UserModel::update($where, $updateData);

                        // set json data
                        $this->json = [
                            'status' => true,
                            'data'   => UserModel::infoFull(['users.id' => $body['id']])
                        ];
                    } else {
                        // set json data
                        $this->json = [
                            'status'  => false,
                            'message' => 'Пользователь не найден'
                        ];
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
                    'message' => 'Required user ID and updated DATA'
                ];
            }

            // return response json data
            return $response->withJson($this->json);
        }

        /**
         * User Sign In
         */
        public function signIn($request, $response, $args)
        {
            $body = $request->getParsedBody();

            // post body get data
            $email = filter_var(mb_strtolower(trim($body['email']), 'UTF-8'), FILTER_SANITIZE_EMAIL);
            $pass  = strip_tags(trim($body['pass']));
            
            // validate body
            if($email != '' && $pass != '') {
                if(filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
                    $this->validate = true;
                } else {
                    // set json data
                    $this->json = [
                        'status'  => false,
                        'message' => 'Неверный электронной почты'
                    ];
                }
            } else {
                // set json data
                $this->json = [
                    'status'  => false,
                    'message' => 'Пожалуйста, не оставляйте пустые строки пустыми'
                ];
            }

            // if validation status true
            if($this->validate === true) {
                try {
                    $userInfo = UserModel::infoFull([
                        ['users.email', '=', $email],
                        ['users.pass', '=', md5($pass)]
                    ]);
                    if($userInfo !== false) {
                        // set json data
                        $this->json = [
                            'status' => true,
                            'data'   => $userInfo
                        ];
                    } else {
                        // set json data
                        $this->json = [
                            'status'  => false,
                            'message' => 'Пользователь не найден'
                        ];
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                    // set json data
                    $this->json = [
                        'status'  => false,
                        'message' => 'Database Error: ' . $e->getMessage()
                    ];
                }
            }

            // return response json data
            return $response->withJson($this->json);
        }

        /**
         * User Sign Up
         */
        public function signUp($request, $response, $args)
        {
            $body = $request->getParsedBody();

            // post body get data
            $email    = filter_var(mb_strtolower(trim($body['email']), 'UTF-8'), FILTER_SANITIZE_EMAIL);
            $username = mb_strtolower(trim($body['username']), 'UTF-8');
            $pass     = strip_tags(trim($body['pass']));
            $ref_code = strip_tags(trim($body['ref_code']));

            // validate body
            if($email != '' && $username != '' && $pass != '') {
                if(filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
                    if(preg_match('/^[a-z0-9_-]{3,15}$/i', $username)) {
                        if(strlen($pass) >= 6) {
                            $this->validate = true;
                        } else {
                            // set json data
                            $this->json = [
                                'status'  => false,
                                'message' => 'Пароль должен содержать не менее 6 символов'
                            ];
                        }
                    } else {
                        // set json data
                        $this->json = [
                            'status'  => false,
                            'message' => 'Неверное имя пользователя'
                        ];
                    }
                } else {
                    // set json data
                    $this->json = [
                        'status'  => false,
                        'message' => 'Неверный электронной почты'
                    ];
                }
            } else {
                // set json data
                $this->json = [
                    'status'  => false,
                    'message' => 'Пожалуйста, не оставляйте пустые строки пустыми'
                ];
            }

            // if validation status true
            if($this->validate === true) {
                try {
                    $existEmail    = UserModel::exist(['email' => $email]);
                    $existUsername = UserModel::exist(['username' => $username]);

                    // database insert status
                    $insert = true;

                    if($existEmail === true) {
                        // set json data
                        $this->json = [
                            'status'  => false,
                            'message' => 'Этот адрес электронной почты уже используется'
                        ];
                        // set insert status
                        $insert = false;
                    } elseif($existUsername === true) {
                        // set json data
                        $this->json = [
                            'status'  => false,
                            'message' => 'Имя пользователя уже используется'
                        ];
                        // set insert status
                        $insert = false;
                    } elseif($ref_code != '') {
                        $refInfo = UserModel::info(['referral_code' => $ref_code], ['id']);
                        if($refInfo === false) {
                            // set json data
                            $this->json = [
                                'status'  => false,
                                'message' => 'Код реферала не найден'
                            ];
                            // set insert status
                            $insert = false;
                        }
                    }

                    if($insert === true) {
                        // modify insert data
                        $insertData                  = $body;
                        $insertData['pass']          = md5($insertData['pass']);
                        $insertData['heart']         = 3;
                        $insertData['register_time'] = time();

                        // destroy ref_code from $insertData
                        unset($insertData['ref_code']);

                        // insert user
                        $lastId = UserModel::insert($insertData);
                        if($lastId > 0) {
                            // update referral code
                            UserModel::update(['id' => $lastId], [
                                'referral_code' => str_pad($lastId, 6, '0', STR_PAD_LEFT)
                            ]);

                            // insert referral
                            if($ref_code != '') {
                                ReferralModel::insert([
                                    'user_id'     => $lastId,
                                    'ref_user_id' => $refInfo->id,
                                    'time'        => time()
                                ]);
                            }
                        }
                        
                        // set json data
                        $this->json = [
                            'status' => true,
                            'data'   => UserModel::infoFull([
                                ['users.email', '=', $email],
                                ['users.pass', '=', md5($pass)]
                            ])
                        ];
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                    // set json data
                    $this->json = [
                        'status'  => false,
                        'message' => 'Database Error: ' . $e->getMessage()
                    ];
                }
            }

            // return response json data
            return $response->withJson($this->json);
        }
    }

?>