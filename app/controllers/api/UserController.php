<?php

    namespace App\Controllers\Api;

    use App\Models\Api\UserModel;

    class UserController
    {
        private $json     = [];
        private $validate = false;

        public function getUser($request, $response, $args)
        {
            $params = $request->getQueryParams();
            
            if(@$params['id'] > 0) {
                $where = [];
                foreach ($params as $key => $value) {
                    $where[] = [$key, '=', $value];
                }
    
                try {
                    $userInfo = UserModel::getUser($where);
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
                            'message' => 'User not exist'
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
                    'message' => 'User not exist'
                ];
            }

            // return reponse json data
            return $response->withJson($this->json);
        }

        public function updateUser($request, $response, $args)
        {
            $body = $request->getParsedBody();

            if($body['id'] > 0 && count($body['data']) > 0) {
                
                $updateData = [];
                foreach ($body['data'] as $key => $value) {
                    $updateData[$key] = $value;
                }

                try {
                    $where = ['id' => $body['id']];

                    // update user
                    UserModel::updateUser($where, $updateData);

                    // get user information
                    $userInfo = UserModel::getUser($where);
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
                            'message' => 'User not exist'
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

            // return reponse json data
            return $response->withJson($this->json);
        }

        /**
         * User Sign In
         */
        public function signIn($request, $response, $args)
        {
            $body = $request->getParsedBody();

            // post body get data
            $email = filter_var(mb_strtolower($body['email'], 'UTF-8'), FILTER_SANITIZE_EMAIL);
            $pass  = $body['pass'];
            
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
                    $userInfo = UserModel::getUser([
                        ['email', '=', $email],
                        ['pass', '=', md5($pass)]
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

            // return reponse json data
            return $response->withJson($this->json);
        }

        /**
         * User Sign Up
         */
        public function signUp($request, $response, $args)
        {
            $body = $request->getParsedBody();

            // post body get data
            $email    = filter_var(mb_strtolower($body['email'], 'UTF-8'), FILTER_SANITIZE_EMAIL);
            $username = mb_strtolower($body['username'], 'UTF-8');
            $pass     = $body['pass'];

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
                    }

                    if($insert === true) {
                        // modify insert data
                        $insertData          = $body;
                        $insertData['pass']  = md5($insertData['pass']);
                        $insertData['heart'] = 3;

                        // insert user
                        UserModel::insertUser($insertData);
                        
                        // set json data
                        $this->json = [
                            'status' => true,
                            'data'   => UserModel::getUser([
                                ['email', '=', $email],
                                ['pass', '=', md5($pass)]
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

            // return reponse json data
            return $response->withJson($this->json);
        }
    }

?>