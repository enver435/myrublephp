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
    }

?>