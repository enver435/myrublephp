<?php

    namespace App\Controllers\Api;

    use App\Models\Api\WithdrawModel;
    use App\Controllers\BaseController;

    class WithdrawController extends BaseController
    {
        private $json = [];

        /**
         * Get Withdraws
         */
        public function withdraws($request, $response, $args)
        {
            // get query params
            $params = $request->getQueryParams();

            $where = null;
            if(!empty($params)) {
                foreach ($params as $key => $value) {
                    if($key != 'offset' && $key != 'limit' && isset($value) && $value != '') {
                        // set where
                        $where[] = [$key, '=', $value];
                    }
                }
            }

            try {
                // set json data
                $this->json = [
                    'status' => true,
                    'data'   => WithdrawModel::withdraws($where, @$params['offset'], @$params['limit'])
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
         * Insert Withdraw
         */
        public function insertWithdraw($request, $response, $args)
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
                $lastId     = WithdrawModel::insert($data);
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

        /**
         * Payment Methods
         */
        public function paymentMethods($request, $response, $args)
        {
            try {
                // set json data
                $this->json = [
                    'status' => true,
                    'data'   => WithdrawModel::paymentMethods()
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