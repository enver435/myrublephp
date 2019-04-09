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
        public static function withdraws($where = null, $offset = null, $limit = null)
        {
            // select table
            $query = self::get('db')->table('withdraws');

            // if where not null
            if($where != null) {
                $query->where($where);
            }

            // order by id desc
            $query->orderBy('id', 'desc')->get();

            // if exist pagination
            if($offset >= 0 && $limit > 0) {
                $query->offset($offset)->limit($limit);
            }

            // get results
            $results = $query->get();
            
            // return results
            return $results;
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
    }

?>