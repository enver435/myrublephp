<?php

    namespace App\Models\Dashboard;
    use App\Models\BaseModel;

    class PrizeRefModel extends BaseModel
    {
        /**
         * Get Levels
         *
         * @return array
         */
        public static function all()
        {
            return self::get('db')->table('prizes_referral')
                ->get();
        }

        /**
         * Get Level Information
         *
         * @param array $where
         * @return object
         */
        public static function info($where)
        {
            $result = self::get('db')->table('prizes_referral')
                ->where($where)
                ->first();
            
            if(!empty($result)) {
                return $result;
            }
            return false;
        }

        /**
         * Update Level
         *
         * @param array $where
         * @param array $data
         * @return boolean
         */
        public function update($where, $data)
        {
            return self::get('db')->table('prizes_referral')
                ->where($where)
                ->update($data);
        }

        /**
         * Insert Level
         *
         * @param array $data
         * @return integer
         */
        public static function insert($data)
        {
            return self::get('db')->table('prizes_referral')
                ->insertGetId($data);
        }

        /**
         * Delete Level
         *
         * @param array $where
         * @return boolean
         */
        public static function delete($where)
        {
            return self::get('db')->table('prizes_referral')
                ->where($where)
                ->delete();
        }
    }

?>