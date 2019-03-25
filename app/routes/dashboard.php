<?php

    /**
     * DASHBOARD Routes
     */
    $app->group('/dashboard', function() {
        // index
        $this->map(['GET', 'POST'], '', '\App\Controllers\Dashboard\MainController:index')->setName('dashboard.index');
        // users
        $this->group('/users', function() {
            // index
            $this->get('', '\App\Controllers\Dashboard\UserController:index')->setName('dashboard.users.index');
            // edit
            $this->map(['GET', 'POST'], '/edit/{id}', '\App\Controllers\Dashboard\UserController:edit')->setName('dashboard.users.edit');
            // show
            $this->get('/show/{id}', '\App\Controllers\Dashboard\UserController:show')->setName('dashboard.users.show');
        });
        // withdraws
        $this->group('/withdraws', function() {
            // index
            $this->get('', '\App\Controllers\Dashboard\WithdrawController:index')->setName('dashboard.withdraw.index');
            // edit
            $this->map(['GET', 'POST'], '/edit/{id}', '\App\Controllers\Dashboard\WithdrawController:edit')->setName('dashboard.withdraw.edit');
        });
    });

?>