<?php

    namespace App\System\Helpers;

    class Main
    {
        /**
         * Get Ip
         *
         * @param boolean $serverIp
         * @return string
         */
        public static function getIp($serverIp = false)
        {
            if($serverIp === false) {
                if (!empty(self::getServer('HTTP_CLIENT_IP'))) {
                    $ip = self::getServer('HTTP_CLIENT_IP');
                } elseif (!empty(self::getServer('HTTP_X_FORWARDED_FOR'))) {
                    $ip = self::getServer('HTTP_X_FORWARDED_FOR');
                } else {
                    $ip = self::getServer('REMOTE_ADDR');
                }
            } else {
                $ip = self::getServer('SERVER_ADDR');
            }
            return $ip == '::1' ? '127.0.0.1' : $ip;
        }

        /**
         * Get Global Server Variable
         *
         * @param string $name
         * @return boolean|string|array
         */
        public static function getServer($name)
        {
            $name = strtoupper($name);
            if(isset($_SERVER[$name])) {
                return $_SERVER[$name];
            }
            return false;
        }
    }

?>