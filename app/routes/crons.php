<?php

    /**
     * Crons Routes
     */
    $app->group('/crons', function() {
        // heart notify
        $this->get('/heart-notify', '\App\Controllers\Crons\NotifyController:heart');

        // withdraw notify
        $this->group('/withdraw', function() {
            // yandex
            $this->get('/yandex', '\App\Controllers\Crons\WithdrawController:withdrawYandex');
            // payeer
            $this->get('/payeer', '\App\Controllers\Crons\WithdrawController:withdrawPayeer');
        });
    });

?>