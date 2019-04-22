<?php

    namespace App\Models\Api;
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

            // if where not null
            if($where != null) {
                $query->where($where);
            }

            // order by id desc
            $query->orderBy('id', 'desc')->get();

            // if exist limit
            if($limit > 0 && $offset >= 0) {
                $query->limit($limit)->offset($offset);
            }
            
            // return rows
            return $query->get();
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
         * Payment Method Information
         *
         * @param integer $method
         * @return object
         */
        public static function methodInfo($method)
        {
            return self::get('db')->table('payment_methods')
                ->where(['method' => $method])
                ->first();
        }
    }

?>