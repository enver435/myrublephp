<?php

    $app->map(['GET', 'POST'], '/', function($request, $response, $args) {
        return $response->getBody()->write(getenv('APP_NAME') . ' Version: ' . getenv('APP_VERSION'));
    });

    // Auth Routes
    $app->group('/auth', function() {
        // sign in
        $this->post('/signin', '\App\Controllers\AuthController:signIn');
    });

?>