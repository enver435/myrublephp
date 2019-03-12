<?php

    namespace App\Controllers;

    use App\Models\UserModel;

    use Kreait\Firebase;
    use Kreait\Firebase\ServiceAccount;

    class CronController
    {
        private $json = [];
        private $firebase;

        public function __construct()
        {
            $serviceAccount = ServiceAccount::fromJsonFile('myruble-dfbba-firebase-adminsdk-sri1t-d6e6fb83e7.json');
            $firebase = (new Firebase\Factory())
                ->withServiceAccount($serviceAccount)
                ->create();
            $this->firebase = $firebase;
        }

        public function heartNotify($request, $response, $args)
        {
            $nowTime = time();

            try {
                $users = UserModel::users([
                    ['notify_heart_time', '>', 0]
                ]);

                foreach ($users as $user) {
                    if($nowTime >= $user->notify_heart_time) {
                        // create notification
                        $title = 'Возможность играть';
                        $body  = 'У вас есть 1 шанс начать игру прямо сейчас!';

                        try {
                            $this->firebase
                                ->getMessaging()
                                ->send([
                                    'token' => $user->firebase_token,
                                    'notification' => [
                                        'title' => $title,
                                        'body' => $body,
                                    ],
                                    'android' => [
                                        'priority' => 'normal',
                                        'notification' => [
                                            'title'      => $title,
                                            'body'       => $body,
                                            'channel_id' => 'myruble_channel',
                                            'sound'      => 'default',
                                            'color'      => '#474747'
                                        ]
                                    ]
                                ]);
    
                            // update user
                            UserModel::updateUser(['id' => $user->id], [
                                'notify_heart_time' => 0
                            ]);
                        } catch (\Throwable $th) {
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

            // return reponse json data
            return $response->withJson($this->json);
        }
    } 

?>