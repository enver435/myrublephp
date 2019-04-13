<?php

    namespace App\Models\Dashboard;

    use App\Models\BaseModel;

    class UserModel extends BaseModel
    {
        /**
         * Get All Users
         *
         * @return array
         */
        public static function users($where = null, $offset = null, $limit = null)
        {
            // select table
            $query = self::get('db')->table('users');

            // select columns
            $query->selectRaw('
                users.*,
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
            $query->leftJoin('game_levels', function($join) {
                $join->on('game_levels.level_start_xp', '<=', 'users.level_xp')
                    ->on('game_levels.level_end_xp', '>', 'users.level_xp');
            });

            // if where not null
            if($where != null) {
                $query->where($where);
            }

            // order by id asc
            $query->orderBy('users.id', 'asc')->get();

            // if exist pagination
            if($offset >= 0 && $limit > 0) {
                $query->offset($offset)->limit($limit);
            }
            
            // get rows
            $results = $query->get();

            // return results
            return $results;
        }

        /**
         * Get User Information
         *
         * @param string|array $where
         * @return object
         */
        public static function info($where)
        {
            $result = self::get('db')->table('users')
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
            $query->leftJoin('game_levels', function($join) {
                $join->on('game_levels.level_start_xp', '<=', 'users.level_xp')
                    ->on('game_levels.level_end_xp', '>', 'users.level_xp');
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