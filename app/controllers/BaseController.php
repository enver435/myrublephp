<?php

    namespace App\Controllers;
    use \Psr\Container\ContainerInterface as Container;
    use App\Models\BaseModel;

    class BaseController
    {
        protected $container;

        public function __construct(Container $container)
        {
            $this->container = $container;

            // add view global variables
            $this->addGlobalView();
        }

        private function addGlobalView()
        {
            // header withdraws count
            $this->container->view->getEnvironment()->addGlobal('headerWithdraws', [
                'all'      => BaseModel::count('withdraws'),
                'waiting'  => BaseModel::count('withdraws', ['payment_status' => 0]),
                'paid'     => BaseModel::count('withdraws', ['payment_status' => 1]),
                'not_paid' => BaseModel::count('withdraws', ['payment_status' => 2])
            ]);
        }

        public function __get($property)
        {
            if(isset($this->container->{$property})) {
                return $this->container->{$property};
            }
        }
    }

?>