<?php

    namespace App\Controllers\Api;
    use App\Models\Api\GameModel;
    use App\Controllers\BaseController;
    use App\Models\Api\UserModel;

    class GameController extends BaseController
    {
        private $json = [];

        /**
         * Get Game Levels
         */
        public function gameLevels($request, $response, $args)
        {
            try {
                // set json data
                $this->json = [
                    'status' => true,
                    'data'   => GameModel::gameLevels()
                ];
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

        /**
         * Insert Game Result
         */
        public function insertGame($request, $response, $args)
        {
            $body = $request->getParsedBody();

            // get post body
            $user_id = $body['user_id'];
            $task_success = $body['task_success'];
            $task_fail = $body['task_fail'];
            $answer_click_count = $body['answer_click_count'];

            if($user_id > 0 && $task_success >= 0 && $task_fail >= 0 && $answer_click_count >= 0) {
                try {
                    // get max level information
                    $maxLevel = GameModel::gameLevels(true);
    
                    // get user information
                    $userInfo = UserModel::infoFull(['users.id' => $user_id]);
    
                    if($userInfo !== false && $userInfo->ban == 0) {
                        // get current level information
                        $levelData = GameModel::levelInfo(['level' => $userInfo->level]);
    
                        if($levelData !== false) {
                            
                            $level_xp = 0;
                            $earn = 0;
                            $earn_referral = 0;
                            $status = 0;
    
                            if(
                                $levelData->task == $task_success &&
                                $answer_click_count >= $levelData->task
                            ) {
                                $earn = $levelData->earn;
                                $status = 1;
                                
                                // eger en sonuncu levelin bitis xpsi boyukduse istifadecinin levelinin xpsinden
                                if($maxLevel->level_end_xp > $userInfo->level_xp) {
                                    $level_xp = $levelData->earn_xp;
                                }
                                
                                if($userInfo->ref_user_id > 0) {
                                    $earn_referral = $levelData->earn * $levelData->referral_percent / 100;
                                }
                            }
    
                            // insert game
                            $lastID = GameModel::insert([
                                'user_id' => $user_id,
                                'task_success' => $task_success,
                                'task_fail' => $task_fail,
                                'earn' => $earn,
                                'earn_referral' => $earn_referral,
                                'status' => $status,
                                'time' => time()
                            ]);
    
                            if($lastID > 0) {
                                // update user for me
                                UserModel::update(['id' => $user_id], [
                                    'balance' => $this->db->raw('balance + ' . $earn),
                                    'level_xp' => $this->db->raw('level_xp + ' . $level_xp)
                                ]);
    
                                // update user for referral
                                if($userInfo->ref_user_id > 0) {
                                    UserModel::update(['id' => $userInfo->ref_user_id], [
                                        'balance' => $this->db->raw('balance + ' . $earn_referral)
                                    ]);
                                }
    
                                // set json data
                                $this->json = [
                                    'status' => true,
                                    'data' => UserModel::infoFull(['users.id' => $user_id])
                                ];
                            } else {
                                // set json data
                                $this->json = [
                                    'status'  => false,
                                    'message' => 'Error: Insert Game'
                                ];
                            }
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

            // return reponse json data
            return $response->withJson($this->json);
        }
    }

?>