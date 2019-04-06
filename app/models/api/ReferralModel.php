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
        public static function referrals($user_id, $offset = null, $limit = null)
        {
            // select table
            $query = self::get('db')->table('user_referrals');

            // select columns
            $query->selectRaw('
                user_referrals.*,
                users.username,
                users.level_xp,
                (
                    CASE WHEN game_levels.level IS NULL
                    THEN
                        (SELECT level FROM game_levels ORDER BY level DESC LIMIT 1)
                    ELSE
                        game_levels.level
                    END
                ) AS level,
                (
                    CASE WHEN game_levels.referral_percent IS NULL
                    THEN
                        (SELECT referral_percent FROM game_levels ORDER BY level DESC LIMIT 1)
                    ELSE
                        game_levels.referral_percent
                    END
                ) AS referral_percent,
                IFNULL(ROUND(SUM(game_logs.earn_referral), 3), 0) AS total_earn_referral
            ');

            // join tables
            $query->join('users', function($join) {
                $join->on('user_referrals.user_id', '=', 'users.id');
            })
            ->leftJoin('game_levels', function($join) {
                $join->on('game_levels.level_start_xp', '<=', 'users.level_xp')
                    ->on('game_levels.level_end_xp', '>', 'users.level_xp');
            })
            ->leftJoin('game_logs', function($join) {
                $join->on('game_logs.user_id', '=', 'user_referrals.user_id')
                    ->where('game_logs.earn_referral', '>', 0);
            });

            // where columns
            $query->where(['user_referrals.ref_user_id' => $user_id]);

            // order by
            $query->orderBy('user_referrals.id', 'desc');

            // group by
            $query->groupBy('user_referrals.id');

            // get results
            $results = $query->get();

            // if exist pagination
            if($offset >= 0 && $limit > 0) {
                $query->offset($offset)->limit($limit);
            }

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