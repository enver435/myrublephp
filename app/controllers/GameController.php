<?php

    namespace App\Controllers;

    use App\Models\GameModel;

    class GameController
    {
        private $json = [];

        /**
         * Get Game Default
         */
        public function getDefault($request, $response, $args)
        {
            try {
                // set json data
                $this->json = [
                    'status' => true,
                    'data'   => GameModel::getDefault()
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

            $data = [];
            foreach ($body as $key => $value) {
                $data[$key] = $value;
            }

            try {
                $lastId     = GameModel::insertGame($data);
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