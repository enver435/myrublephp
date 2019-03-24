<?php

    /**
     * DASHBOARD Routes
     */
    $app->group('/dashboard', function() {
        // index
        $this->map(['GET', 'POST'], '', '\App\Controllers\Dashboard\MainController:index');
    });

?>