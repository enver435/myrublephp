<?php

    namespace App\System\Helpers;

    class Session
    {
        /**
         * Get Session
         *
         * @param string $key
         * @return any
         */
        public static function get($key)
        {
            if(isset($_SESSION[$key])) {
                return $_SESSION[$key];
            }
            return false;
        }

        /**
         * Set Session
         *
         * @param string $key
         * @param any $value
         * @return void
         */
        public static function set($key, $value)
        {
            $_SESSION[$key] = $value;
        }

        /**
         * Update Session
         *
         * @param string $key
         * @param any $value
         * @return boolean
         */
        public static function update($key, $value)
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
                self::set($key, $replacedValue);
            }
            return true;
        }

        /**
         * Destroy Session
         *
         * @param string $key
         * @return boolean
         */
        public static function destroy($key)
        {
            if(isset($_SESSION[$key])) {
                unset($_SESSION[$key]);
            }
            return true;
        }
    }

?>