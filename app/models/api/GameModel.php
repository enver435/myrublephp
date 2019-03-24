<?php

    namespace App\Models\Api;

    use App\Models\BaseModel;

    class GameModel extends BaseModel
    {
        /**
         * Get Game Default
         *
         * @return object
         */
        public static function getDefault()
        {
            $result = self::get('db')->table('game')
                ->first();
            
            if(!empty($result)) {
                return $result;
            }
            return false;
        }

        /**
         * Insert Game
         *
         * @param array $data
         * @return integer
         */
        public static function insertGame($data)
        {
            return self::get('db')->table('game_logs')
                ->insertGetId($data);
        }
    }

?>