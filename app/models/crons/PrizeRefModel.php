<?php

    namespace App\Models\Crons;
    use App\Models\BaseModel;

    class PrizeRefModel extends BaseModel
    {
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
                        users.firebase_token,
                        users.locale,
                        COUNT(user_referrals.user_id) AS total_ref_count
                    ')
                    ->join('users', function($join) {
                        $join->on('user_referrals.ref_user_id', '=', 'users.id');
                    })
                    ->where([
                        ['users.ban', '=', 0],
                        ['user_referrals.time', '>=', $prize->start_time],
                        ['user_referrals.time', '<=', $prize->end_time]
                    ])
                    ->groupBy('users.id')
                    ->orderBy('total_ref_count', 'desc')
                    ->get();
            }
            return [];
        }

        /**
         * Update Prize
         *
         * @param array $where
         * @param array $data
         * @return boolean
         */
        public static function update($where, $data)
        {
            return self::get('db')->table('prizes_referral')
                ->where($where)
                ->update($data);
        }
    }

?>