<?php

    namespace App\Controllers\Api;
    use App\Controllers\BaseController;
    use App\Models\Api\PrizeRefModel;

    class PrizeRefController extends BaseController
    {
        private $json = [];

        /**
         * Get Active Prize
         */
        public function activePrize($request, $response, $args)
        {
            try {
                // set json data
                $this->json = [
                    'status' => true,
                    'data'   => [
                        'prizeInfo' => PrizeRefModel::activePrize(),
                        'prizeRefs' => PrizeRefModel::prizeRefs()
                    ]
                ];
            } catch (\Illuminate\Database\QueryException $e) {
                // set json data
                $this->json = [
                    'status'  => false,
                    'message' => 'Database Error: ' . $e->getMessage()
                ];
            }

            // return response json data
            return $response->withJson($this->json);
        }
    }

?>