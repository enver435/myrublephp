<?php

    namespace App\Models\Api;
    use App\Models\BaseModel;

    class PrizeRefModel extends BaseModel
    {
        /**
         * Get Prizes
         *
         * @return array
         */
        public static function prizes()
        {
            return self::get('db')->table('prizes_referral')->get();
        }

        /**
         * Get Active Prize
         *
         * @return object
         */
        public static function activePrize()
        {
            $result = self::get('db')->table('prizes_referral')
                ->where([
                    ['end_time', '>=', time()],
                    ['status', '=', 1]
                ])
                ->first();
                
            if(!empty($result)) {
                return $result;
            }
            return false;
        }

        /**
         * Get Active Prize Refs
         *
         * @return array
         */
        public static function prizeRefs()
        {
            $prize = self::activePrize();
            if($prize) {
                return self::get('db')->table('user_referrals')
                    ->selectRaw('
                        users.id,
                        users.referral_code,
                        users.username,
                        COUNT(user_referrals.user_id) AS total_ref_count
                    ')
                    ->join('users', function($join) {
                        $join->on('user_referrals.ref_user_id', '=', 'users.id');
                    })
                    ->where([
                        ['user_referrals.time', '>=', $prize->start_time],
                        ['user_referrals.time', '<=', $prize->end_time]
                    ])
                    ->groupBy('users.id')
                    ->orderBy('total_ref_count', 'desc')
                    ->get();
            }
            return [];
        }
    }

?>