<?php

    namespace App\Models;

    class UserModel extends BaseModel
    {
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