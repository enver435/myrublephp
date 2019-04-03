<?php

    namespace App\Controllers\Api;

    use App\Models\Api\GameModel;
    use App\Controllers\BaseController;

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
                $insertData         = $data;
                $insertData['time'] = time();
                GameModel::insert($insertData);

                // set json data
                $this->json = [
                    'status' => true
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