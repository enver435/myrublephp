<?php

    namespace App\System\Helpers;

    class Url
    {
        /**
         * Redirect URL
         *
         * @param string $url
         * @return void
         */
        public static function redirect($routeName = null, $data = [])
        {
            $response = $GLOBALS['container']->get('response');
            return $response->withRedirect(self::pathFor($routeName ?? 'index', $data));
        }

        /**
         * Get Base URL
         *
         * @param string $path
         * @return string
         */
        public static function baseUrl($path = null)
        {
            return getenv('APP_URL') . ($path !== null ? '/' . $path : '');
        }

        /**
         * Get URl for Route Name
         *
         * @param string $name
         * @param array $data
         * @param array $queryParams
         * @return string
         */
        public static function pathFor($name, $data = [], $queryParams = [])
        {
            return self::baseUrl() . $GLOBALS['container']->get('router')->pathFor($name, $data, $queryParams);
        }
    }
    
?>