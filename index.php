<?php

    require_once './vendor/autoload.php';

    // start session
    session_start();

    switch ($_SERVER['SERVER_NAME']) {
        case 'localhost':
        case '192.168.0.102':
            $env = 'dev';
            break;
        
        default:
            $env = 'production';
            break;
    }

    // load .env file
    $dotenv = Dotenv\Dotenv::create(__DIR__, $env == 'dev' ? '.env.dev' : '.env');
    $dotenv->load(true);

    // instantiate the App object
    $app = new \Slim\App([
        'settings' => [
            'determineRouteBeforeAppMiddleware' => true,
            'displayErrorDetails' => filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN),
            'db' => [
                'driver'    => 'mysql',
                'host'      => getenv('DB_HOST'),
                'database'  => getenv('DB_NAME'),
                'username'  => getenv('DB_USER'),
                'password'  => getenv('DB_PASS'),
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
            ]
        ]
    ]);
   
    // require containers
    require_once 'app/containers.php';

    // require middleware
    require_once 'app/middleware.php';
    
    // require routes
    require_once 'app/routes.php';

    // run application
    $app->run();

?>