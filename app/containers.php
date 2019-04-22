<?php

    use App\Models\BaseModel;

    $container = $app->getContainer();
    unset($app->getContainer()['errorHandler']);
    unset($app->getContainer()['phpErrorHandler']);

    // exception handler
    // $container['errorHandler'] = function($container) {
    //     return function($request, $response, $exception) use($container) {
    //         $data = [
    //             'status'  => false,
    //             'code'    => $exception->getCode(),
    //             'message' => $exception->getMessage(),
    //             'file'    => $exception->getFile(),
    //             'line'    => $exception->getLine(),
    //             'trace'   => explode("\n", $exception->getTraceAsString()),
    //         ];
    //         return $response->withJson($data);
    //     };
    // };

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

    // twig view
    $container['view'] = function ($container) {
        // get view directory
        $viewDir = __DIR__ . '/view';

        // set view directory
        $view = new \Slim\Views\Twig($viewDir);
        
        $router = $container->get('router');
        $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
        $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
        $view->addExtension(new \Twig_Extension_Debug());
        $view->addExtension(new \App\System\Extensions\TwigExtension($container));

        // all view render
        $view['siteName'] = getenv('APP_NAME');
        $view['baseUrl']  = getenv('APP_URL');
        $view['fullUrl']  = $container->get('request')->getUri();
        $view['session']  = $_SESSION;
        $view['cookie']   = $_COOKIE;
        $view['env']      = ENVIRONMENT;

        // header withdraws count
        $view['headerWithdraws'] = [
            'all'      => BaseModel::count('withdraws'),
            'waiting'  => BaseModel::count('withdraws', ['payment_status' => 0]),
            'paid'     => BaseModel::count('withdraws', ['payment_status' => 1]),
            'not_paid' => BaseModel::count('withdraws', ['payment_status' => 2])
        ];

        return $view;
    };

    // flash message
    $container['flash'] = function () {
        return new \Slim\Flash\Messages();
    };

    // container set $GLOBALS variable
    $GLOBALS['container'] = $container;

?>