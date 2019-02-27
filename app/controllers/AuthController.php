<?php

    namespace App\Controllers;

    use App\Models\UserModel;

    class AuthController
    {
        private $json     = [];
        private $validate = false;

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
                        'message' => 'Email formatı düzgün deyil'
                    ];
                }
            } else {
                // set json data
                $this->json = [
                    'status'  => false,
                    'message' => 'Sətirləri boş buraxmayın'
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
                            'message' => 'İstifadəçi tapılmadı'
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
            if($username != '' && $email != '' && $pass != '') {
                if(filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
                    if(preg_match('/^[a-z0-9_-]{3,15}$/i', $username)) {
                        $this->validate = true;
                    } else {
                        // set json data
                        $this->json = [
                            'status'  => false,
                            'message' => 'İstifadəçi adı formatı düzgün deyil'
                        ];
                    }
                } else {
                    // set json data
                    $this->json = [
                        'status'  => false,
                        'message' => 'Email formatı düzgün deyil'
                    ];
                }
            } else {
                // set json data
                $this->json = [
                    'status'  => false,
                    'message' => 'Sətirləri boş buraxmayın'
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
                            'message' => 'Bu email artıq istifadə olunur'
                        ];
                        // set insert status
                        $insert = false;
                    } elseif($existUsername === true) {
                        // set json data
                        $this->json = [
                            'status'  => false,
                            'message' => 'Bu istifadəçi adı artıq istifadə olunur'
                        ];
                        // set insert status
                        $insert = false;
                    }

                    if($insert === true) {
                        // insert user
                        UserModel::insertUser([
                            'email'    => $email,
                            'username' => $username,
                            'pass'     => md5($pass),
                            'heart'    => 3
                        ]);
                        
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
                        'status'  => 'error',
                        'message' => 'Database Error: ' . $e->getMessage()
                    ];
                }
            }

            // return reponse json data
            return $response->withJson($this->json);
        }
    }

?>