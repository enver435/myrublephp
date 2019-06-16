<?php

    namespace App\Controllers\Dashboard;
    use App\Controllers\BaseController;
    use App\System\Helpers\Url;
    use App\System\Libraries\Firebase;
    use App\Models\Dashboard\UserModel;

    class NotifyController extends BaseController
    {
        public function index($request, $response, $args)
        {
            if($request->isPost()) {
                // get post body
                $body = $request->getParsedBody();
                
                $locale = strip_tags(trim($body['locale']));
                $title = strip_tags(trim($body['title']));
                $message = strip_tags(trim($body['message']));

                if($locale != '' && $title != '' && $message != '') {
                    // init firebase
                    $firebase = Firebase::init();
                    
                    // get users firebase token
                    $tokens = [];
                    $users = UserModel::users(['locale' => $locale]);
                    foreach ($users as $user) {
                        $tokens[] = $user->firebase_token;
                    }

                    // send notification
                    $firebase->sendMultiNotify($tokens, $title, $message);

                    // add flash message
                    $this->flash->addMessage('danger', 'Göndərildi');

                    // redirect page
                    return Url::redirect('dashboard.send_notify');
                }
            }

            // render page
            return $this->view->render($response, 'dashboard/send_notify.html');
        }
    }

?>