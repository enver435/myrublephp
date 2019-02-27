<?php

    namespace App\Models;

    class AuthModel extends BaseModel
    {
        /**
         * Get User
         *
         * @param string|array $where
         * @return object
         */
        public static function getUser($where)
        {
            return self::get('db')->table('users')
                ->where($where)
                ->first();
        }

        /**
         * Insert User
         *
         * @param array $data
         * @return integer
         */
        public function insertUser($data)
        {
            return self::get('db')->table('users')
                ->insertGetId($data);
        }
    }

?>