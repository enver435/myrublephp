<?php

    namespace App\Models;

    class WithdrawModel extends BaseModel
    {
        /**
         * Get Withdraws
         *
         * @return object
         */
        public static function withdraws($user_id, $payment_status, $offset, $limit)
        {
            $query = self::get('db')->table('withdraws');

            if($user_id > 0) {
                $query->where(['user_id' => $user_id]);
            }

            if($payment_status) {
                $query->where(['payment_status' => $payment_status]);
            }

            if($offset >= 0 && $limit > 0) {
                $query->offset($offset)->limit($limit);
            }

            $result = $query->orderBy('id', 'desc')->get();
            
            if(!empty($result)) {
                return $result;
            }
            return false;
        }

        /**
         * Payment Methods
         *
         * @return object
         */
        public static function paymentMethods()
        {
            return self::get('db')->table('payment_methods')
                ->first();
        }

        /**
         * Insert Withdraw
         *
         * @param array $data
         * @return integer
         */
        public static function insert($data)
        {
            return self::get('db')->table('withdraws')
                ->insertGetId($data);
        }
    }

?>