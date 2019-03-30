<?php

    use App\System\Helpers\Session;
    use App\System\Helpers\Url;

    /**
     * Dashboard Middleware
     */
    $mw = function($request, $response, $next) {
        if(Session::get('login')) {
            return $next($request, $response);
        } else if($request->getAttribute('route')->getName() != 'dashboard.index') {
            return Url::redirect('dashboard.index');
        }
        return $next($request, $response);
    };

    /**
     * DASHBOARD Routes
     */
    $app->group('/dashboard', function() {
        // index
        $this->map(['GET', 'POST'], '', '\App\Controllers\Dashboard\MainController:index')->setName('dashboard.index');

        // logout
        $this->get('/logout', '\App\Controllers\Dashboard\MainController:logout')->setName('dashboard.logout');
        
        /**
         * Users Routes
         */
        $this->group('/users', function() {
            // index
            $this->get('', '\App\Controllers\Dashboard\UserController:index')->setName('dashboard.users.index');
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
            $this->get('', '\App\Controllers\Dashboard\WithdrawController:index')->setName('dashboard.withdraws.index');
            // edit
            $this->map(['GET', 'POST'], '/edit/{id}', '\App\Controllers\Dashboard\WithdrawController:edit')->setName('dashboard.withdraws.edit');
        });

        /**
         * Settings Routes
         */
        $this->group('/settings', function() {
            // game settings
            $this->map(['GET', 'POST'], '/game', '\App\Controllers\Dashboard\SettingsController:game')->setName('dashboard.settings.game');
            // payment method settings
            $this->map(['GET', 'POST'], '/payment-methods', '\App\Controllers\Dashboard\SettingsController:paymentMethods')->setName('dashboard.settings.payment_methods');
        });

    })->add($mw);

?>