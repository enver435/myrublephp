<?php

    // Last trailing slash middleware
    $app->add(function ($request, $response, $next) {
        $uri = $request->getUri();
        $path = $uri->getPath();
        if ($path != '/' && substr($path, -1) == '/') {
            // permanently redirect paths with a trailing slash
            // to their non-trailing counterpart
            $uri = $uri->withPath(substr($path, 0, -1));
            
            if($request->getMethod() == 'GET') {
                return $response->withRedirect((string)$uri, 301);
            } else {
                return $next($request->withUri($uri), $response);
            }
        }
        return $next($request, $response);
    });

    // Add locale middleware
    $app->add(new App\System\Middleware\Locale([
        'allowedLocales' => $allowedLocales,
        'defaultLocale' => $defaultLocale
    ]));

?>