<?php

    namespace App\Controllers\Api;
    use App\Models\Api\UserModel;
    use App\Controllers\BaseController;
    use App\Models\Api\ReferralModel;
    use App\System\Helpers\Email;
    use App\System\Helpers\Main;

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
                if($key != 'full' && $key != 'locale') {
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
                            'message' => $this->trans('api/user.not_found')
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
                    'message' => $this->trans('api/user.not_found')
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
                            'message' => $this->trans('api/user.user_not_found')
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
            $email = mb_strtolower(trim($body['email']), 'UTF-8');
            $pass  = strip_tags(trim($body['pass']));
            
            // validate body
            if($email != '' && $pass != '') {
                if(Email::valid($email)) {
                    $this->validate = true;
                } else {
                    // set json data
                    $this->json = [
                        'status'  => false,
                        'message' => $this->trans('api/user.email_wrong')
                    ];
                }
            } else {
                // set json data
                $this->json = [
                    'status'  => false,
                    'message' => $this->trans('api/user.empty')
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
                        if($userInfo->ban == 0) {
                            // set json data
                            $this->json = [
                                'status' => true,
                                'data'   => $userInfo
                            ];
                        } else {
                            // set json data
                            $this->json = [
                                'status' => false,
                                'message' => $this->trans('api/user.banned')
                            ];
                        }
                    } else {
                        // set json data
                        $this->json = [
                            'status'  => false,
                            'message' => $this->trans('api/user.not_found')
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
            $email    = mb_strtolower(trim($body['email']), 'UTF-8');
            $username = mb_strtolower(trim($body['username']), 'UTF-8');
            $pass     = strip_tags(trim($body['pass']));
            $ref_code = strip_tags(trim($body['ref_code']));
            $mac_address = strip_tags(trim($body['mac_address']));
            $device_id = strip_tags(trim($body['device_id']));
            $timezone = strip_tags(trim($body['timezone']));

            // validate body
            if($email != '' && $username != '' && $pass != '') {
                $mac_address = UserModel::exist(['mac_address' => $mac_address]);
                $ip_address = UserModel::exist(['ip_address' => Main::getIp()]);
                $device_id = UserModel::exist(['device_id' => $device_id]);
                if($mac_address || $ip_address || $device_id) {
                    // set json data
                    $this->json = [
                        'status'  => false,
                        'message' => $this->trans('api/user.reg_once_only')
                    ];
                } else {
                    if(Email::valid($email)) {
                        if(preg_match('/^[a-z0-9_-]{3,15}$/i', $username)) {
                            if(strlen($pass) >= 6) {
                                $this->validate = true;
                            } else {
                                // set json data
                                $this->json = [
                                    'status'  => false,
                                    'message' => $this->trans('api/user.pass_wrong')
                                ];
                            }
                        } else {
                            // set json data
                            $this->json = [
                                'status'  => false,
                                'message' => $this->trans('api/user.not_found')
                            ];
                        }
                    } else {
                        // set json data
                        $this->json = [
                            'status'  => false,
                            'message' => $this->trans('api/user.email_wrong')
                        ];
                    }
                }

            } else {
                // set json data
                $this->json = [
                    'status'  => false,
                    'message' => $this->trans('api/user.empty')
                ];
            }

            // if validation status true
            if($this->validate) {
                try {
                    $existEmail    = UserModel::exist(['email' => $email]);
                    $existUsername = UserModel::exist(['username' => $username]);

                    // database insert status
                    $insert = true;

                    if($existEmail) {
                        // set json data
                        $this->json = [
                            'status'  => false,
                            'message' => $this->trans('api/user.email_exist')
                        ];
                        // set insert status
                        $insert = false;
                    } elseif($existUsername) {
                        // set json data
                        $this->json = [
                            'status'  => false,
                            'message' => $this->trans('api/user.username_exist')
                        ];
                        // set insert status
                        $insert = false;
                    } elseif($ref_code != '') {
                        $refUserInfo = UserModel::info(['referral_code' => $ref_code], ['id']);
                        if($refUserInfo === false || $refUserInfo->ban == 1) {
                            // set json data
                            $this->json = [
                                'status'  => false,
                                'message' => $this->trans('api/referral.refcode_not_found')
                            ];
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
                            'referrer'       => 1, // app
                            'mac_address'    => $mac_address,
                            'ip_address'     => Main::getIp(),
                            'device_id'      => $device_id,
                            'timezone'       => $timezone
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