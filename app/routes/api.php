<?php

    use App\System\Helpers\Main;

    $mw = function($request, $response, $next) {
        if(ENVIRONMENT == 'production') {
            // if(
            //     Main::getServer('PHP_AUTH_USER') == getenv('API_USER') &&
            //     Main::getServer('PHP_AUTH_PW') == getenv('API_PASS') ||
            //     $request->getUri()->getPath() == 'api' // burani yadda saxla sonra silersen
            // ) {
            //     $response = $next($request, $response);
            // } else {
            //     $response = $response->withStatus(401)->withHeader('WWW-Authenticate', 'Basic realm="Protected Area"');
            // }
            $response = $next($request, $response);
        } else {
            $response = $next($request, $response);
        }
        return $response;
    };

    /**
     * API Routes
     */
    $app->group('/api', function() {
        /**
         * Index
         */
        $this->map(['GET', 'POST'], '', function($request, $response, $args) {
            return $response->withJson([
                'status' => true,
                'data'   => [
                    'appName'    => getenv('APP_NAME'),
                    'appVersion' => getenv('APP_VERSION'),
                    'appStatus'  => getenv('APP_STATUS'),
                    'appTime'    => time()
                ]
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
            $this->get('/info', '\App\Controllers\Api\UserController:userInfo');
            // update user
            $this->post('/update', '\App\Controllers\Api\UserController:updateUser');
        });
    
        /**
         * GAME Routes
         */
        $this->group('/game', function() {
            // get game default info
            $this->get('/levels', '\App\Controllers\Api\GameController:gameLevels');
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

        /**
         * REFERRAL Routes
         */
        $this->group('/referral', function() {
            // get referral
            $this->get('', '\App\Controllers\Api\ReferralController:referrals');
            // insert referral
            $this->post('/insert', '\App\Controllers\Api\ReferralController:insertReferral');
        });
    })->add($mw);

?>