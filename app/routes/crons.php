<?php

    /**
     * Crons Routes
     */
    $app->group('/crons', function() {
        // heart notify
        $this->get('/heart-notify', '\App\Controllers\Crons\NotifyController:heart');

        // withdraw notify
        $this->group('/withdraw', function() {
            // payeer
            $this->get('/payeer', '\App\Controllers\Crons\WithdrawController:withdrawPayeer');
        });
    });

?>