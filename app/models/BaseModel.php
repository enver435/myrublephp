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
    }

?>