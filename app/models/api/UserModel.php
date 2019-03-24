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
         * Get User
         *
         * @param string|array $where
         * @return object
         */
        public static function getUser($where)
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
         * Update User
         *
         * @param string|array $where
         * @return object
         */
        public static function updateUser($where, $data)
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

        /**
         * Insert User
         *
         * @param array $data
         * @return integer
         */
        public static function insertUser($data)
        {
            return self::get('db')->table('users')
                ->insertGetId($data);
        }
    }

?>