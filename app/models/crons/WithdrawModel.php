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
    }

?>