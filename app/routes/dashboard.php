<?php

    use App\System\Helpers\Session;
    use App\System\Helpers\Url;

    /**
     * Dashboard Middleware
     */
    $mw = function($request, $response, $next) {
        if(Session::get('login')) {
            return $next($request, $response);
        } else if($request->getAttribute('route')->getName() != 'dashboard') {
            return Url::redirect('dashboard');
        }
        return $next($request, $response);
    };

    /**
     * DASHBOARD Routes
     */
    $app->group('/dashboard', function() {
        // index
        $this->map(['GET', 'POST'], '', '\App\Controllers\Dashboard\MainController:index')->setName('dashboard');

        // logout
        $this->get('/logout', '\App\Controllers\Dashboard\MainController:logout')->setName('dashboard.logout');
        
        /**
         * Users Routes
         */
        $this->group('/users', function() {
            // index
            $this->get('', '\App\Controllers\Dashboard\UserController:index')->setName('dashboard.users');
            // edit
            $this->map(['GET', 'POST'], '/edit/{id}', '\App\Controllers\Dashboard\UserController:edit')->setName('dashboard.users.edit');
            // show
            $this->get('/show/{id}', '\App\Controllers\Dashboard\UserController:show')->setName('dashboard.users.show');
        });

        /**
         * Withdraws Routes
         */
        $this->group('/withdraws', function() {
            // index
            $this->get('', '\App\Controllers\Dashboard\WithdrawController:index')->setName('dashboard.withdraws');
            // edit
            $this->map(['GET', 'POST'], '/edit/{id}', '\App\Controllers\Dashboard\WithdrawController:edit')->setName('dashboard.withdraws.edit');
            // payment methods
            $this->map(['GET', 'POST'], '/payment-methods', '\App\Controllers\Dashboard\WithdrawController:paymentMethods')->setName('dashboard.withdraws.payment_methods');
        });

        /**
         * Levels Routes
         */
        $this->group('/prize-ref', function() {
            // index
            $this->get('', '\App\Controllers\Dashboard\PrizeRefController:index')->setName('dashboard.prizeref');
            // add
            $this->map(['GET', 'POST'], '/add', '\App\Controllers\Dashboard\PrizeRefController:add')->setName('dashboard.prizeref.add');
            // edit
            $this->map(['GET', 'POST'], '/edit/{id}', '\App\Controllers\Dashboard\PrizeRefController:edit')->setName('dashboard.prizeref.edit');
            // delete
            $this->get('/delete/{id}', '\App\Controllers\Dashboard\PrizeRefController:delete')->setName('dashboard.prizeref.delete');
        });

        /**
         * Levels Routes
         */
        $this->group('/levels', function() {
            // index
            $this->get('', '\App\Controllers\Dashboard\LevelController:index')->setName('dashboard.levels');
            // add
            $this->map(['GET', 'POST'], '/add', '\App\Controllers\Dashboard\LevelController:add')->setName('dashboard.levels.add');
            // edit
            $this->map(['GET', 'POST'], '/edit/{id}', '\App\Controllers\Dashboard\LevelController:edit')->setName('dashboard.levels.edit');
            // delete
            $this->get('/delete/{id}', '\App\Controllers\Dashboard\LevelController:delete')->setName('dashboard.levels.delete');
        });

    })->add($mw);

?>