<?php

    namespace App\Controllers;

    use App\Models\WithdrawModel;

    class WithdrawController
    {
        private $json = [];

        /**
         * Get Withdraws
         */
        public function withdraws($request, $response, $args)
        {
            $params = $request->getQueryParams();

            try {
                // set json data
                $this->json = [
                    'status' => true,
                    'data'   => WithdrawModel::withdraws(@$params['user_id'], @$params['payment_status'], @$params['offset'], @$params['limit'])
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