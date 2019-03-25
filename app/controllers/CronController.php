<?php

    namespace App\Controllers;

    use App\Models\Api\UserModel;
    use App\System\Libraries\Firebase;

    class CronController
    {
        private $json = [];

        public function heartNotify($request, $response, $args)
        {
            // get now time
            $nowTime  = time();

            // init firebase
            $firebase = Firebase::init();

            try {
                // get users
                $users = UserModel::users([
                    ['notify_heart_time', '>', 0]
                ]);

                foreach ($users as $user) {
                    if($nowTime >= $user->notify_heart_time) {
                        // create notification
                        $title = 'Возможность играть';
                        $body  = 'У вас есть 1 шанс начать игру прямо сейчас!';

                        try {
                            // send notification
                            $firebase->sendNotify($user->firebase_token, $title, $body);
    
                            // update user
                            UserModel::update(['id' => $user->id], [
                                'notify_heart_time' => 0
                            ]);
                        } catch (\Throwable $e) {
                            // set json data
                            $this->json = [
                                'status'  => false,
                                'message' => 'Error: ' . $e->getMessage()
                            ];
                        }
                    }
                }
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