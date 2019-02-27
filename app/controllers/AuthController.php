<?php

    namespace App\Controllers;

    class AuthController
    {
        private $json = [];

        public function signIn($request, $response, $args)
        {
            $body = $request->getParsedBody();

            // post body
            $email = $body['email'];
            $pass  = $body['pass'];

            if(filter_var($email, FILTER_SANITIZE_EMAIL) !== false && $pass != '')
            {
                
            }
            else
            {
                $this->json = [
                    'status' => 'error',
                    'message' => 'Sətirləri '
                ];
            }
        }
    }

?>