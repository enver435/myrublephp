<?php

    namespace App\Controllers;

    use \Psr\Container\ContainerInterface as Container;

    class BaseController
    {
        protected $container;

        public function __construct(Container $container)
        {
            $this->container = $container;
        }

        public function __get($property)
        {
            if(isset($this->container->{$property})) {
                return $this->container->{$property};
            }
        }
    }

?>