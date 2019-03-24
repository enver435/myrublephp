<?php

    /**
     * Crons Routes
     */
    $app->group('/crons', function() {
        // heart notify
        $this->get('/heart-notify', '\App\Controllers\CronController:heartNotify');
    });

?>