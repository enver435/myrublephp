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
        });
    });

?>