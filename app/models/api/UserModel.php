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
            $result = self::get('db')->table('users')
                ->selectRaw('
                    users.*,
                    user_referrals.ref_user_id,
                    COUNT(user_referrals.id) AS total_referral,
                    CASE
                        WHEN (SUM(game_logs.earn_referral) IS NULL) THEN 0
                        ELSE SUM(game_logs.earn_referral)
                    END AS total_earn_referral
                ')
                ->join('user_referrals', function($join) {
                    $join->on('user_referrals.user_id', '=', 'users.id');
                })
                ->join('game_logs', function($join) {
                    $join->on('game_logs.user_id', '=', 'user_referrals.user_id');
                })
                ->where($where)
                ->first();
            
            if(!empty($result)) {
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

        /**
         * Insert Referral
         *
         * @param array $data
         * @return integer
         */
        public static function insertReferral($data)
        {
            return self::get('db')->table('user_referals')
                ->insertGetId($data);
        }
    }

?>