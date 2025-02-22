<?php

    namespace App\Models\Crons;
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

            // if exist limit
            if($limit > 0 && $offset >= 0) {
                $query->limit($limit)->offset($offset);
            }
            
            // return rows
            return $query->get();
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
         * @return boolean
         */
        public static function update($where, $data)
        {
            return self::get('db')->table('withdraws')
                ->where($where)
                ->update($data);
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