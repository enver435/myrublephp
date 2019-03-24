<?php
    
    namespace App\System\Helpers;

    class Cookie
    {
        /**
         * Get Cookie
         *
         * @param string $key
         * @return boolean|string|array
         */
        public static function get($key)
        {
            if(isset($_COOKIE[$key])) {
                return $_COOKIE[$key];
            }
            return false;
        }

        /**
         * Set Cookie
         *
         * @param string $key
         * @param boolean|string|array $value
         * @param integer $time
         * @param string $path
         * @return boolean
         */
        public static function set($key, $value, $time = 3600, $path = '/')
        {
            return setcookie($key, $value, time() + $time, $path);
        }

        /**
         * Update Cookie
         *
         * @param string $key
         * @param boolean|string|array $value
         * @param integer $time
         * @param string $path
         * @return boolean
         */
        public static function update($key, $value, $time = 3600, $path = '/')
        {
            $get = self::get($key);
            if($get !== false) {
                $gettype = gettype($get);
                // if data object, convert object to array
                if($gettype == 'object') {
                    $get = json_decode(json_encode($get), true);
                }
                $replacedValue = array_replace_recursive($get, $value);
                // if data is object, convert array to object
                if($gettype == 'object') {
                    $replacedValue = json_decode(json_encode($replacedValue));
                }
                self::set($key, $replacedValue, $time, $path);
            }
            return true;
        }

        /**
         * Destroy Cookie
         *
         * @param string $key
         * @return boolean
         */
        public static function destroy($key)
        {
            if(isset($_COOKIE[$key])) {
                unset($_COOKIE[$key]);
                setcookie($key, null, -1);
            }
            return true;
        }
    }

?>