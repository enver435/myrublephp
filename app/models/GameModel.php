<?php

    namespace App\Models;

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
    }

?>