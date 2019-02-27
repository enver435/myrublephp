<?php

    $container = $app->getContainer();

    // exception handler
    $container['errorHandler'] = function($container) {
        return function($request, $response, $exception) use($container) {
            $data = [
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'trace'   => explode("\n", $exception->getTraceAsString()),
            ];

            $container->view->render($response, '_errors/all.html', [
                'error' => $data
            ]);
            return $response->withStatus(500);
        };
    };

    // page not found handler
    $container['notFoundHandler'] = function ($container) {
        return function ($request, $response) use($container) {
            $container->view->render($response, '_errors/404.html', [
                'error' => []
            ]);
            return $response->withStatus(404);
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