<?php

    require_once './vendor/autoload.php';

    // start session
    session_start();

    // set default time zone
    date_default_timezone_set('Asia/Baku');

    // get environment
    switch ($_SERVER["HTTP_HOST"]) {
        case "localhost":
        case "192.168.0.101":
        case "192.168.0.102":
        case "192.168.1.101":
        case "192.168.1.104":
        case "192.168.1.106":
        case "192.168.1.109":
            define('ENVIRONMENT', 'development');
            break;
        default:
            define('ENVIRONMENT', 'production');
            break;
    }

    // load .env file
    $dotenv = Dotenv\Dotenv::create(__DIR__, ENVIRONMENT == 'development' ? '.env.dev' : '.env');
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