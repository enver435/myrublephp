<?php

    namespace App\Controllers\Crons;
    use App\Models\Crons\UserModel;
    use App\System\Libraries\Firebase;
    use App\Controllers\BaseController;

    class NotifyController extends BaseController
    {
        public function heart($request, $response, $args)
        {
            // init firebase
            $firebase = Firebase::init();

            // get users
            $users = UserModel::users([
                ['notify_heart_time', '>', 0],
                ['notify_heart_time', '<=', time()]
            ], 15);
            
            if(count($users) > 0) {
                foreach ($users as $user) {
                    // update user
                    UserModel::update(['id' => $user->id], [
                        'notify_heart_time' => 0
                    ]);

                    // send notification
                    $title = 'Возможность играть';
                    $body  = 'У вас есть 1 шанс начать игру прямо сейчас!';
                    $firebase->sendNotify($user->firebase_token, $title, $body);
                }
            }
        }
    } 

?>