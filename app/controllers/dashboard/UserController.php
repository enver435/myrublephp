<?php

    namespace App\Controllers\Dashboard;

    use App\Controllers\BaseController;

    class UserController extends BaseController
    {
        public function index($request, $response, $args)
        {
            echo 'users index';
        }
    }

?>