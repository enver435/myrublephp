<?php

    namespace App\Models\Api;

    use App\Models\BaseModel;

    class GameModel extends BaseModel
    {
        /**
         * Get Game Levels
         *
         * @return object
         */
        public static function gameLevels()
        {
            $results = self::get('db')->table('game_levels')
                ->get();
            
            if(!empty($results)) {
                return $results;
            }
            return false;
        }

        /**
         * Insert Game
         *
         * @param array $data
         * @return integer
         */
        public static function insert($data)
        {
            return self::get('db')->table('game_logs')
                ->insertGetId($data);
        }
    }

?>