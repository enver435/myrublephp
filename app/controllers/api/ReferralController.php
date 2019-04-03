<?php

    namespace App\Controllers\Api;

    use App\Models\Api\ReferralModel;
    use App\Controllers\BaseController;

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

            // set array body data
            $data = [];
            foreach ($body as $key => $value) {
                if(isset($body[$key]['currentTime']) && $body[$key]['currentTime'] == "true") {
                    $data[$key] = time();
                } else {
                    $data[$key] = $value;
                }
            }

            try {
                $lastId     = ReferralModel::insert($data);
                $data['id'] = $lastId;
                
                // set json data
                $this->json = [
                    'status' => true,
                    'data'   => $data
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
    }

?>