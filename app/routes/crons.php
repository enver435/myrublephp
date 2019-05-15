<?php

    /**
     * Crons Routes
     */
    $app->group('/crons', function() {
        // heart notify
        $this->get('/heart-notify', '\App\Controllers\Crons\NotifyController:heart');

        // prize referral
        $this->get('/prize-ref', '\App\Controllers\Crons\NotifyController:prizeRef');

        // withdraw routes
        $this->group('/withdraw', function() {
            // yandex
            $this->get('/yandex', '\App\Controllers\Crons\WithdrawController:withdrawYandex');
            // payeer
            $this->get('/payeer', '\App\Controllers\Crons\WithdrawController:withdrawPayeer');
            // webmoney
            $this->get('/webmoney', '\App\Controllers\Crons\WithdrawController:withdrawWebmoney');
        });
    });

?>