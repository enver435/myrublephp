<?php

    /*
    * File: Locale.php
    * File Created: Saturday, 30th March 2019 11:38:16 am
    * Author: Anvar Abbasov (anvar.z.abbasov@gmail.com)
    */

    // namespace App\System\Middleware;

    // use Slim\Http\Request;
    // use App\System\Helpers\Cookie;

    // class Locale
    // {
    //     private $allowedLocales;
    //     private $defaultLocale;

    //     public function __construct($settings)
    //     {
    //         $this->allowedLocales = $settings['allowedLocales'];
    //         $this->defaultLocale  = $settings['defaultLocale'];
    //     }
        
    //     public function __invoke($request, $response, $next)
    //     {
    //         $locale = $request->getParam('hl');
    //         if(isset($locale) && !empty($locale)) {
    //             if($this->ifLangExist($locale, $this->allowedLocales)) {
    //                 if(Cookie::get('locale') != $locale) {
    //                     Cookie::set('locale', $locale);
    //                 }
    //             }
    //             return $response->withRedirect($request->getHeader('HTTP_REFERER')[0] ?? $_ENV['APP_URL']);
    //         } else {
    //             return $next($request, $response);
    //         }
    //     }

    //     public function ifLangExist($lang)
    //     {
    //         if (in_array($lang, $this->allowedLocales)) {
    //             return true;
    //         } else {
    //             return false;
    //         }
    //     }
    // }

    namespace App\System\Middleware;
    
    use Slim\Http\Request;
    use App\System\Helpers\Session;

    class Locale
    {
        private $requestLocale = "";
        private $requestUrl;
        private $allowedLocales;
        private $defaultLocale;

        public function __construct($settings)
        {
            $this->allowedLocales = $settings['allowedLocales'];
            $this->defaultLocale = $settings['defaultLocale'];
        }

        public function __invoke($request, $response, $next)
        {
            $this->requestLocale = $this->getWantLangFromUrlPath($request) == '' ? $this->defaultLocale : $this->getWantLangFromUrlPath($request);
            $this->requestUrl = $request->getUri()->getPath();

            // request
            if($this->requestUrl[0] == '/') {
                $this->requestUrl = substr($this->requestUrl, 1);
            }

            if ($this->requestUrl == '/' || empty($this->requestUrl) || $this->localeMatch($this->requestUrl)) {
                if (!empty($this->requestLocale) && $this->ifLangExist($this->requestLocale) == true) {
                    if(Session::get('locale') != $this->requestLocale) {
                        Session::set('locale', $this->requestLocale);
                    }
                }
            }

            // set locale
            $locale = Session::get('locale') ?? $this->defaultLocale;
            setlocale(LC_ALL, $locale . '.UTF-8');

            // return
            return $next($request, $response);
        }

        public function ifLangExist($lang)
        {
            if (in_array($lang, $this->allowedLocales)) {
                return true;
            } else {
                return false;
            }
        }

        public function localeMatch($url)
        {
            if(preg_match("/(^[a-z]{2}$)|(^[a-z]{2}\/)/", $url, $matches, PREG_OFFSET_CAPTURE, 0)) {
                return true;
            }
            return false;
        }

        public function getWantLangFromUrlPath(Request $request)
        {
            $url = $request->getUri()->getPath();
            if ($url[0] == '/') {
                $url = substr($url, 1);
            }
            return explode('/', $url)[0];
        }
    }

?>
