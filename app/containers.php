<?php

    $container = $app->getContainer();

    // exception handler
    $container['errorHandler'] = function($container) {
        return function($request, $response, $exception) use($container) {
            $data = [
                'status'  => false,
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'trace'   => explode("\n", $exception->getTraceAsString()),
            ];
            return $response->withJson($data);
        };
    };

    // page not found handler
    $container['notFoundHandler'] = function ($container) {
        return function ($request, $response) use($container) {
            $data = [
                'status' => false,
                'code' => '404',
                'message' => 'Route not found'
            ];
            return $response->withJson($data);
        };
    };

    // database
    $container['db'] = function ($container) {
        $capsule = new \Illuminate\Database\Capsule\Manager;
        $capsule->addConnection($container['settings']['db']);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        return $capsule->getConnection();
    };

    // container set $GLOBALS variable
    $GLOBALS['container'] = $container;

?>