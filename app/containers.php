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

    // twig view
    $container['view'] = function ($container) {
        // get view directory
        $viewDir = __DIR__ . '/view';

        // set view directory
        $view = new \Slim\Views\Twig($viewDir);
        
        // add view extenstions
        $router = $container->get('router');
        $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
        $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
        $view->addExtension(new \Twig_Extension_Debug());
        $view->addExtension(new \App\System\Extensions\TwigExtension($container));
        
        // add view global variables
        $view->getEnvironment()->addGlobal('siteName', getenv('APP_NAME'));
        $view->getEnvironment()->addGlobal('baseUrl', getenv('APP_URL'));
        $view->getEnvironment()->addGlobal('fullUrl', $container->get('request')->getUri());
        $view->getEnvironment()->addGlobal('locale', $_SESSION['locale'] ?? $container->get('settings')['defaultLocale']);
        $view->getEnvironment()->addGlobal('session', $_SESSION);
        $view->getEnvironment()->addGlobal('cookie', $_COOKIE);
        $view->getEnvironment()->addGlobal('env', ENVIRONMENT);

        return $view;
    };

    // translator
    $container['translator'] = function($container) {
        $settings   = $container->get('settings');

        if(strpos($container->get('request')->getUri()->getPath(), 'api') !== false) {
            $locale = getallheaders()['locale'] ?? $settings['defaultLocale'];
        } else {
            $locale = $_SESSION['locale'] ?? $settings['defaultLocale'];
        }

        $loader     = new Illuminate\Translation\FileLoader(new Illuminate\Filesystem\Filesystem(), __DIR__ . '/translations');
        $translator = new Illuminate\Translation\Translator($loader, $locale);
        $translator->setFallback($settings['defaultLocale']);
        return $translator;
    };

    // flash message
    $container['flash'] = function () {
        return new \Slim\Flash\Messages();
    };

    // container set $GLOBALS variable
    $GLOBALS['container'] = $container;

?>