<?php

    namespace App\System\Extensions;

    use Twig_Extension;
    use Twig_SimpleFunction;
    use Twig_Filter;
    use App\System\Helpers\Main;
    use App\System\Helpers\Url;

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
                new Twig_SimpleFunction('isPost', [$this, 'isPost']),
                new Twig_SimpleFunction('inJson', [$this, 'inJson']),
                new Twig_SimpleFunction('storagePath', [$this, 'storagePath']),
                new Twig_SimpleFunction('publicPath', [$this, 'publicPath'])
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
         * Exist In Json
         *
         * @param json $json
         * @param string $search
         * @return boolean
         */
        public function inJson($json, $search)
        {
            $json = json_decode($json, true);
            if(in_array($search, $json)) {
                return true;
            }
            return false;
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

        /**
         * Get Storage Path
         *
         * @param string $file
         * @param string $path
         * @return string
         */
        public function storagePath($path)
        {
            return Url::baseUrl('storage/' . $path);
        }

        /**
         * Get Public Path
         *
         * @param string $file
         * @param string $path
         * @return string
         */
        public function publicPath($path = '')
        {
            return Url::baseUrl('public/' . $path);
        }
    }

?>