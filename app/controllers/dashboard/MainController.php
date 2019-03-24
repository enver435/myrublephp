<?php

    namespace App\Controllers\Dashboard;

    use App\Controllers\BaseController;

    class MainController extends BaseController
    {
        public function index($request, $response, $args)
        {
            return $this->view->render($response, 'dashboard/index.html');
        }
    }

?>