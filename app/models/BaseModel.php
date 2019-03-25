<?php

    namespace App\Models;

    Class BaseModel
    {
        /**
         * Get Container
         *
         * @param string $container
         * @return void
         */
        protected static function get($container)
        {
            return $GLOBALS['container']->get($container);
        }

        /**
         * Get count
         *
         * @param string $table
         * @param array|string $where
         * @return integer
         */
        public static function count($table, $where = null)
        {
            $totalItems = self::get('db')->table($table);
            if($where !== null) {
                $totalItems->where($where);
            }
            return $totalItems->count('id');
        }

        /**
         * Sum Column
         *
         * @param string $table
         * @param string $column
         * @param array|string $where
         * @return integer
         */
        public static function sum($table, $column, $where = null)
        {
            $totalItems = self::get('db')->table($table);
            if($where !== null) {
                $totalItems->where($where);
            }
            return $totalItems->sum($column);
        }
    }

?>