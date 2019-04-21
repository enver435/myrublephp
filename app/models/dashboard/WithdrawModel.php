<?php

    namespace App\Models\Dashboard;
    use App\Models\BaseModel;

    class WithdrawModel extends BaseModel
    {
        /**
         * Get Withdraws
         *
         * @return array
         */
        public static function withdraws($where = null, $limit = 0, $offset = 0)
        {
            // select table
            $query = self::get('db')->table('withdraws');

            // select columns
            $query->selectRaw('
                withdraws.*,
                users.username AS username,
                users.referrer AS referrer
            ');

            // if where not null
            if($where != null) {
                $query->where($where);
            }

            // if exist limit
            if($limit > 0 && $offset >= 0) {
                $query->limit($limit)->offset($offset);
            }

            // relation user table
            $query->join('users', 'users.id', '=', 'withdraws.user_id');

            // order by id desc
            $results = $query->orderBy('withdraws.id', 'desc')->get();
            
            // return results
            return $results;
        }

        /**
         * Get Withdraw Information
         *
         * @param string|array $where
         * @return object
         */
        public function info($where)
        {
            $result = self::get('db')->table('withdraws')
                ->where($where)
                ->first();
            
            if(!empty($result)) {
                return $result;
            }
            return false;
        }

        /**
         * Update Withdraw
         *
         * @param string|array $where
         * @param array $data
         * @return boolean
         */
        public function update($where, $data)
        {
            return self::get('db')->table('withdraws')
                ->where($where)
                ->update($data);
        }

        /**
         * Payment Methods
         *
         * @return array
         */
        public static function paymentMethods()
        {
            return self::get('db')->table('payment_methods')
                ->get();
        }

        /**
         * Update Payment Method
         *
         * @param integer $methodID
         * @param array $data
         * @return boolean
         */
        public function updateMethod($methodID, $data)
        {
            return self::get('db')->table('payment_methods')
                ->where(['method' => $methodID])
                ->update($data);
        }
    }

?>