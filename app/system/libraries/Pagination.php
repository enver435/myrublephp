<?php

    namespace App\System\Libraries;
    
    use JasonGrimes\Paginator;

    Class Pagination
    {
        private static $paginator;
        private static $totalItems;
        private static $currentPage;
        private static $perPage;
        private static $offset;

        /**
         * Initialize Pagination
         *
         * @param integer $totalItems
         * @param integer $currentPage
         * @param integer $perPage
         * @param string $urlPattern
         * @return void
         */
        public static function init($totalItems, $currentPage, $perPage, $urlPattern = '')
        {
            self::$totalItems  = $totalItems;
            self::$currentPage = (!$currentPage ? 1 : $currentPage);
            self::$perPage     = $perPage;
            self::$offset      = (self::$currentPage-1) * self::$perPage;
            $urlPattern        = $urlPattern . '/page/(:num)';
            self::$paginator   = new Paginator(self::$totalItems, self::$perPage, self::$currentPage, $urlPattern);
            self::$paginator->setNextText($GLOBALS['container']->translator->trans('main.next'));
            self::$paginator->setPreviousText($GLOBALS['container']->translator->trans('main.prev'));

            return new self;
        }

        /**
         * Get Current Page
         *
         * @return integer
         */
        public function currentPage()
        {
            return self::$currentPage;
        }

        /**
         * Get Limit
         *
         * @return integer
         */
        public function limit()
        {
            return self::$perPage;
        }

        /**
         * Get Offset
         *
         * @return integer
         */
        public function offset()
        {
            return self::$offset;
        }
        
        /**
         * Get Links
         *
         * @return string
         */
        public function links()
        {
            return self::$paginator;
        }
    }

?>