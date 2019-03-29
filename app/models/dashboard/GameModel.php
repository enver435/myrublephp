<?php

    namespace App\Models\Dashboard;

    use App\Models\BaseModel;

    class GameModel extends BaseModel
    {
        /**
         * Get Game Default
         *
         * @return object
         */
        public static function getDefault()
        {
            $result = self::get('db')->table('game')
                ->first();
            
            if(!empty($result)) {
                return $result;
            }
            return false;
        }

        /**
         * Update
         *
         * @param array $data
         * @return boolean
         */
        public function update($data)
        {
            return self::get('db')->table('game')
                ->update($data);
        }
    }

?>