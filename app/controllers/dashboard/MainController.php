<?php

    namespace App\Controllers\Dashboard;

    use App\Controllers\BaseController;
    use App\System\Helpers\Session;
    use App\System\Helpers\Url;

    class MainController extends BaseController
    {
        public function index($request, $response, $args)
        {
            if($request->isPost()) {
                $body = $request->getParsedBody();
                if($body['username'] == getenv('ADMIN_USERNAME') && $body['pass'] == getenv('ADMIN_PASS')) {
                    Session::set('login', true);
                }
            }
            return $this->view->render($response, 'dashboard/index.html');
        }

        public function logout($request, $response, $args)
        {
            if(Session::get('login')) {
                Session::destroy('login');
            }
            return Url::redirect('dashboard.index');
        }
    }

?>