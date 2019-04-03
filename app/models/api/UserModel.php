<?php

    namespace App\Models\Api;

    use App\Models\BaseModel;

    class UserModel extends BaseModel
    {
        /**
         * Get All Users
         *
         * @return array
         */
        public static function users($where = null)
        {
            $results = self::get('db')->table('users');
            if($where != null) {
                $results->where($where);
            }
            return $results->get();
        }

        /**
         * Get User Information
         *
         * @param string|array $where
         * @param array $select
         * @return object
         */
        public static function info($where, $select = ['*'])
        {
            $result = self::get('db')->table('users')
                ->select($select)
                ->where($where)
                ->first();
            
            if(!empty($result)) {
                return $result;
            }
            return false;
        }

        /**
         * Get User Full Information
         *
         * @param string|array $where
         * @return object
         */
        public static function infoFull($where)
        {
            // select table
            $query = self::get('db')->table('users');

            // select columns
            $query->selectRaw('
                users.*,
                refuser.ref_user_id,
                COUNT(DISTINCT refusers.id) AS total_referral,
                IFNULL(ROUND(SUM(game_logs.earn_referral), 2), 0) AS total_earn_referral,
                (
                    CASE WHEN game_levels.level IS NULL
                    THEN
                        (SELECT level FROM game_levels ORDER BY level DESC LIMIT 1)
                    ELSE
                        game_levels.level
                    END
                ) AS level
            ');

            // join tables
            $query->leftJoin('user_referrals AS refuser', function($join) {
                $join->on('refuser.user_id', '=', 'users.id');
            })
            ->leftJoin('user_referrals AS refusers', function($join) {
                $join->on('refusers.ref_user_id', '=', 'users.id');
            })
            ->leftJoin('game_levels', function($join) {
                $join->on('game_levels.level_start_xp', '<=', 'users.level_xp')
                    ->on('game_levels.level_end_xp', '>', 'users.level_xp');
            })
            ->leftJoin('game_logs', function($join) {
                $join->on('game_logs.user_id', '=', 'refusers.user_id')
                    ->where('game_logs.earn_referral', '>', 0);
            });

            // where columns
            $query->where($where);

            // get result
            $result = $query->first();
            
            if($result->id) {
                return $result;
            }
            return false;
        }

        /**
         * Update User
         *
         * @param string|array $where
         * @return object
         */
        public static function update($where, $data)
        {
            return self::get('db')->table('users')
                ->where($where)
                ->update($data);
        }

        /**
         * Insert User
         *
         * @param array $data
         * @return integer
         */
        public static function insert($data)
        {
            return self::get('db')->table('users')
                ->insertGetId($data);
        }

        /**
         * Exist User
         *
         * @param string|array $where
         * @return object
         */
        public static function exist($where)
        {
            $result = self::get('db')->table('users')
                ->where($where)
                ->count('id');
            
            if($result > 0) {
                return true;
            }
            return false;
        }
    }

?>