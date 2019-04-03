<?php

    namespace App\Controllers\Api;

    use App\Controllers\BaseController;
    use App\Models\Api\ReferralModel;
    use App\Models\Api\UserModel;

    class ReferralController extends BaseController
    {
        private $json = [];

        /**
         * Get Referrals
         */
        public function referrals($request, $response, $args)
        {
            // get query params
            $params = $request->getQueryParams();

            try {
                // set json data
                $this->json = [
                    'status' => true,
                    'data'   => ReferralModel::referrals(@$params['user_id'], @$params['offset'], @$params['limit'])
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
         * Insert Referral
         */
        public function insertReferral($request, $response, $args)
        {
            $body = $request->getParsedBody();

            if(
                isset($body['ref_code']) && $body['ref_code'] != '' &&
                isset($body['user_id']) && $body['user_id'] > 0
            ) {
                try {
                    $refInfo = UserModel::info(['referral_code' => $body['ref_code']], ['id', 'referral_code']);
                    if($refInfo !== false) {
                        if($refInfo->id != $body['user_id']) {
                            $lastId = ReferralModel::insert([
                                'user_id'     => $body['user_id'],
                                'ref_user_id' => $refInfo->id,
                                'time'        => time()
                            ]);
    
                            // referral information
                            $data['id']          = $lastId;
                            $data['user_id']     = $body['user_id'];
                            $data['ref_user_id'] = $refInfo->id;
                            $data['time']        = time();
                            
                            // set json data
                            $this->json = [
                                'status' => true,
                                'data'   => $data
                            ];
                        } else {
                            // set json data
                            $this->json = [
                                'status'  => false,
                                'message' => 'Код реферала не найден'
                            ];
                        }
                    } else {
                        // set json data
                        $this->json = [
                            'status'  => false,
                            'message' => 'Код реферала не найден'
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
                    'message' => 'Код реферала не найден'
                ];
            }

            // return reponse json data
            return $response->withJson($this->json);
        }
    }

?>