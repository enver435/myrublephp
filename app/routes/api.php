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
            $this->post('/signin', '\App\Controllers\Api\UserController:signIn');
            // sign up
            $this->post('/signup', '\App\Controllers\Api\UserController:signUp');
        });
    
        /**
         * USER Routes
         */
        $this->group('/user', function() {
            // get user
            $this->get('/info', '\App\Controllers\Api\UserController:getUser');
            // update user
            $this->post('/update', '\App\Controllers\Api\UserController:updateUser');
        });
    
        /**
         * GAME Routes
         */
        $this->group('/game', function() {
            // get default
            $this->get('', '\App\Controllers\Api\GameController:getDefault');
            // insert
            $this->post('/insert', '\App\Controllers\Api\GameController:insertGame');
        });

        /**
         * WITHDRAW Routes
         */
        $this->group('/withdraw', function() {
            // get withdraws
            $this->get('', '\App\Controllers\Api\WithdrawController:withdraws');
            // get payment methods
            $this->get('/methods', '\App\Controllers\Api\WithdrawController:paymentMethods');
            // insert withdraw
            $this->post('/insert', '\App\Controllers\Api\WithdrawController:insertWithdraw');
        });
    });

?>