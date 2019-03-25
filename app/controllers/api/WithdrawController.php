<?php

    namespace App\Controllers\Api;

    use App\Models\Api\WithdrawModel;

    class WithdrawController
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

            // if exist status param
            if(isset($params['status']) && $params['status'] >= 0) {
                // set where
                $where[] = ['payment_status', '=', $params['status']];
            }

            // if exist user_id param
            if(isset($params['user_id']) && $params['user_id'] > 0) {
                // set where
                $where[] = ['user_id', '=', $params['user_id']];
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

            $data = [];
            foreach ($body as $key => $value) {
                $data[$key] = $value;
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