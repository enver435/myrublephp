<?php
    
    use \YandexMoney\API;

    /**
     * Site Routes
     */
    $app->get('/', '\App\Controllers\Site\MainController:index')->setName('index');
    $app->map(['GET', 'POST'], '/register[/{ref_code}]', '\App\Controllers\Site\MainController:register')->setName('register');
    $app->get('/privacy[/{app}]', '\App\Controllers\Site\MainController:privacy')->setName('privacy');

    /**
     * Yandex OAuth
     */
    $app->get('/oauth/yandex', function($request, $response, $args) {
        // // configration
        // $clientId = getenv('YANDEX_CLIENT_ID');
        // $redirectUri = getenv('APP_URL') . '/oauth/yandex';

        // // get query params
        // $params = $request->getQueryParams();

        // if (!empty($params['error'])) {
        //     // Got an error, probably user denied access
        //     exit('Got error: ' . $params['error']);
        // } elseif (empty($params['code'])) {
        //     // If we don't have an authorization code then get one
        //     $scope = ['account-info', 'operation-history', 'operation-details', 'payment-p2p'];
        //     $auth_url = API::buildObtainTokenUrl($clientId, $redirectUri, $scope);
        //     // echo $auth_url;
        //     header('Location: ' . $auth_url);
        //     exit;
        // } else {
        //     $access_token_response = API::getAccessToken($clientId, $params['code'], $redirectUri, null);
        //     $access_token = $access_token_response->access_token;
        //     echo $access_token;
        // }
    })->setName('oauth.yandex');

?>