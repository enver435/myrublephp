<?php

    namespace App\Models\Site;
    use App\Models\BaseModel;

    class ReferralModel extends BaseModel
    {
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