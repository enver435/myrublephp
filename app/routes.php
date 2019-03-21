<?php

    /**
     * API Routes
     */
    $app->group('/api', function() {
        /**
         * Index
         */
        $this->map(['GET', 'POST'], '', function($request, $response, $args) {
            return $response->withJson([
                'appName'    => getenv('APP_NAME'),
                'appVersion' => getenv('APP_VERSION')
            ]);
        });
    
        /**
         * AUTH Routes
         */
        $this->group('/auth', function() {
            // sign in
            $this->post('/signin', '\App\Controllers\UserController:signIn');
            // sign up
            $this->post('/signup', '\App\Controllers\UserController:signUp');
        });
    
        /**
         * USER Routes
         */
        $this->group('/user', function() {
            // get user
            $this->get('/info', '\App\Controllers\UserController:getUser');
            // update user
            $this->post('/update', '\App\Controllers\UserController:updateUser');
        });
    
        /**
         * GAME Routes
         */
        $this->group('/game', function() {
            // get default
            $this->get('', '\App\Controllers\GameController:getDefault');
            // insert
            $this->post('/insert', '\App\Controllers\GameController:insertGame');
        });

        /**
         * WITHDRAW Routes
         */
        $this->group('/withdraw', function() {
            // get withdraws
            $this->get('', '\App\Controllers\WithdrawController:withdraws');
            // get payment methods
            $this->get('/methods', '\App\Controllers\WithdrawController:paymentMethods');
            // insert withdraw
            $this->post('/insert', '\App\Controllers\WithdrawController:insertWithdraw');
        });
    });

    /**
     * Crons Routes
     */
    $app->group('/crons', function() {
        // heart notify
        $this->get('/heart-notify', '\App\Controllers\CronController:heartNotify');
    });

    /**
     * Site Routes
     */
    $app->get('/privacy', function($request, $response, $args) use ($container) {
        return $container->view->render($response, 'privacy.html');
    });

?>