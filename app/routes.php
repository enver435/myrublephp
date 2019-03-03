<?php

    // index
    $app->map(['GET', 'POST'], '/', function($request, $response, $args) {
        return $response->getBody()->write(getenv('APP_NAME') . ' Version: ' . getenv('APP_VERSION'));
    });

    // AUTH Routes
    $app->group('/auth', function() {
        // sign in
        $this->post('/signin', '\App\Controllers\UserController:signIn');
        // sign up
        $this->post('/signup', '\App\Controllers\UserController:signUp');
    });

    // USER Routes
    $app->group('/user', function() {
        // get user information
        $this->post('/info', '\App\Controllers\UserController:getUser');
    });

    // GAME Routes
    $app->group('/game', function() {
        // get default
        $this->post('/default', '\App\Controllers\GameController:getDefault');
    });

?>