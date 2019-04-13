<?php

    namespace App\Controllers\Dashboard;

    use App\Controllers\BaseController;
    use App\System\Helpers\Url;
    use App\Models\Dashboard\WithdrawModel;

    class SettingsController extends BaseController
    {
        public function paymentMethods($request, $response, $args)
        {
            if($request->isPost()) {
                // get post body
                $body = $request->getParsedBody();

                // error
                $error = false;

                foreach ($body['methods'] as $methodID) {
                    try {
                        WithdrawModel::updateMethod($methodID, [
                            'min_withdraw' => $body['min_withdraw'][$methodID],
                            'commission'   => $body['commission'][$methodID],
                            'status'       => (!$body['status'][$methodID] ? 0 : 1)
                        ]);
                    } catch (\Illuminate\Database\QueryException $e) {
                        // add flash message
                        $this->flash->addMessage('danger', 'Database Error: ' . $e->getMessage());

                        // set error
                        $error = true;
                    }
                }

                // check error
                if($error === false) {
                    $this->flash->addMessage('success', 'Məlumatlar yeniləndi');
                }

                // redirect
                return Url::redirect('dashboard.settings.payment_methods');
            }

            $methods = WithdrawModel::paymentMethods();
            return $this->view->render($response, 'dashboard/settings/payment_methods.html', [
                'flash'   => $this->flash->getMessages(),
                'methods' => $methods
            ]);
        }
    }

?>