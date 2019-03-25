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
            $query = self::get('db')->table('users');

            // if where not null
            if($where != null) {
                $query->where($where);
            }

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