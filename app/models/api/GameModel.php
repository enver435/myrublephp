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
            return self::get('db')->table('game_levels')
                ->get();
        }

        /**
         * Get Game Information
         *
         * @param string|array $where
         * @param array $select
         * @return object
         */
        public static function info($where, $select = ['*'])
        {
            $result = self::get('db')->table('game_logs')
                ->select($select)
                ->where($where)
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
        public static function insert($data)
        {
            return self::get('db')->table('game_logs')
                ->insertGetId($data);
        }
    }

?>