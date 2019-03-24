<?php

    namespace App\System\Extensions;

    use Twig_Extension;
    use Twig_SimpleFunction;
    use Twig_Filter;
    use App\System\Helpers\Main;

    class TwigExtension extends Twig_Extension
    {
        protected $container;

        public function __construct($container)
        {
            $this->container = $container;
        }

        /**
         * Get functions
         *
         * @return array
         */
        public function getFunctions()
        {
            return [
                new Twig_SimpleFunction('getEnv', [$this, 'getEnv']),
                new Twig_SimpleFunction('isPost', [$this, 'isPost'])
            ];
        }

        /**
         * Get filters
         *
         * @return array
         */
        public function getFilters()
        {
            return [
                new Twig_Filter('json_decode', [$this, 'jsonDecode'])
            ];
        }

        /**
         * Get json decoded array
         *
         * @param string $json
         * @return array
         */
        public function jsonDecode($json)
        {
            return json_decode($json, true);
        }

        /**
         * Get environment
         *
         * @param string $name
         * @return void
         */
        public function getEnv($name)
        {
            return getenv($name);
        }
        
        /**
         * Check request POST
         *
         * @return boolean
         */
        public function isPost()
        {
            if(Main::getServer('REQUEST_METHOD') == 'POST') {
                return true;
            }
            return false;
        }
    }

?>