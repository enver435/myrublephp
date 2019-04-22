<?php

    namespace App\Models\Api;
    use App\Models\BaseModel;

    class GameModel extends BaseModel
    {
        /**
         * Get Game Levels
         *
         * @param boolean $maxLevel
         * @return array|object
         */
        public static function gameLevels($maxLevel = false)
        {
            $query = self::get('db')->table('game_levels');
            if($maxLevel) {
                $result = $query->orderBy('level', 'desc')->limit(1)->first();
            } else {
                $result = $query->get();
            }
            return $result;
        }

        /**
         * Get Game Level Information
         *
         * @param array $where
         * @return object
         */
        public static function levelInfo($where)
        {
            $result = self::get('db')->table('game_levels')
                ->where($where)
                ->first();
            
            if(!empty($result)) {
                return $result;
            }
            return false;
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