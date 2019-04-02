<?php

    namespace App\Models\Api;

    use App\Models\BaseModel;

    class ReferralModel extends BaseModel
    {
        /**
         * Get Referrals
         *
         * @return array
         */
        public static function referrals($where = null, $offset = null, $limit = null)
        {
            // select table
            $query = self::get('db')->table('user_referrals');

            // if where not null
            if($where != null) {
                $query->where($where);
            }

            // if exist pagination
            if($offset >= 0 && $limit > 0) {
                $query->offset($offset)->limit($limit);
            }

            // order by id desc
            $results = $query->orderBy('id', 'desc')->get();
            
            // return results
            return $results;
        }

        /**
         * Insert Referral
         *
         * @param array $data
         * @return integer
         */
        public static function insert($data)
        {
            return self::get('db')->table('user_referrals')
                ->insertGetId($data);
        }
    }

?>