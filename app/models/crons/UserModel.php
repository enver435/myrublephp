<?php

    namespace App\Models\Crons;
    use App\Models\BaseModel;

    class UserModel extends BaseModel
    {
        /**
         * Get All Users
         *
         * @return array
         */
        public static function users($where = null, $limit = 0, $offset = 0)
        {
            // select table
            $query = self::get('db')->table('users');

            // if where not null
            if($where != null) {
                $query->where($where);
            }

            // if exist limit
            if($limit > 0 && $offset >= 0) {
                $query->limit($limit)->offset($offset);
            }

            // return results
            return $query->get();
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
         * Update User
         *
         * @param array $where
         * @param array $data
         * @return boolean
         */
        public static function update($where, $data)
        {
            return self::get('db')->table('users')
                ->where($where)
                ->update($data);
        }
    }

?>