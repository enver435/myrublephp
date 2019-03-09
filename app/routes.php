<?php

    /**
     * Index
     */
    $app->map(['GET', 'POST'], '/', function($request, $response, $args) {
        return $response->withJson([
            'appName'    => getenv('APP_NAME'),
            'appVersion' => getenv('APP_VERSION')
        ]);
    });

    /**
     * AUTH Routes
     */
    $app->group('/auth', function() {
        // sign in
        $this->post('/signin', '\App\Controllers\UserController:signIn');
        // sign up
        $this->post('/signup', '\App\Controllers\UserController:signUp');
    });

    /**
     * USER Routes
     */
    $app->group('/user', function() {
        // get user
        $this->post('/info', '\App\Controllers\UserController:getUser');
        // update user
        $this->post('/update', '\App\Controllers\UserController:updateUser');
    });

    /**
     * GAME Routes
     */
    $app->group('/game', function() {
        // get default
        $this->post('/default', '\App\Controllers\GameController:getDefault');
        // insert
        $this->post('/insert', '\App\Controllers\GameController:insertGame');
    });

    /**
     * Crons Routes
     */
    $app->group('/crons', function() {
        // heart notify
        $this->get('/heart-notify', '\App\Controllers\CronController:heartNotify');
    });

?>