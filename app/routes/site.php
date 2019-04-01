<?php
    
    /**
     * Site Routes
     */
    $app->get('/privacy', function($request, $response, $args) use ($container) {
        return $container->view->render($response, 'privacy.html');
    });

?>