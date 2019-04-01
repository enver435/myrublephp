<?php

    namespace App\Controllers\Api;

    use App\Models\Api\GameModel;

    class GameController
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

            // set array body data
            $data = [];
            foreach ($body as $key => $value) {
                $data[$key] = $value;
            }

            try {
                $lastId     = GameModel::insert($data);
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