<?php

    namespace App\Models\Site;
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
    }

?>