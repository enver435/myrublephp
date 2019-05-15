<?php

    namespace App\Controllers\Crons;
    use App\Models\Crons\UserModel;
    use App\System\Libraries\Firebase;
    use App\Controllers\BaseController;
    use App\Models\Crons\PrizeRefModel;

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
                    try {
                        // update user
                        UserModel::update(['id' => $user->id], [
                            'notify_heart_time' => 0
                        ]);
    
                        // send notification
                        $title = 'Возможность играть';
                        $body  = 'У вас есть 1 шанс начать игру прямо сейчас!';
                        $firebase->sendNotify($user->firebase_token, $title, $body);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }

        public function prizeRef($request, $response, $args)
        {
            // init firebase
            $firebase = Firebase::init();

            try {
                // get active prize information
                $prize = PrizeRefModel::activePrize();
                if($prize && time() >= $prize->end_time) {
                    // get prize refs
                    $refs = PrizeRefModel::prizeRefs();

                    // update prize
                    PrizeRefModel::update(['id' => $prize->id], [
                        'winner_id' => $refs[0]->id,
                        'status' => 0
                    ]);

                    // send notification
                    $title = 'Вы выиграли приз';
                    $body  = 'Поздравляю. Вы выиграли приз. Сумма приза будет отправлена на ваш баланс как можно скорее';
                    $firebase->sendNotify($refs[0]->firebase_token, $title, $body);
                }
            } catch (\Illuminate\Database\QueryException $e) {
                echo $e->getMessage();
            }
        }
    } 

?>