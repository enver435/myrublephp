<?php

    namespace App\Controllers\Dashboard;
    use App\Controllers\BaseController;
    use App\System\Helpers\Session;
    use App\System\Helpers\Url;
    use App\Models\BaseModel;

    class MainController extends BaseController
    {
        public function index($request, $response, $args)
        {
            if($request->isPost()) {
                $body = $request->getParsedBody();
                if($body['username'] == getenv('ADMIN_USERNAME') && $body['pass'] == getenv('ADMIN_PASS')) {
                    Session::set('login', true);
                }
                return Url::redirect('dashboard');
            }

            $renderData = [];
            if(Session::get('login')) {
                $dayStart = strtotime(date('d.m.Y') . ' 00:00');
                $dayEnd   = strtotime(date('d.m.Y') . ' 23:59');

                /**
                 * Total Analytics
                 */
                $renderData['totalUsers']           = BaseModel::count('users');
                $renderData['totalGameCount']       = BaseModel::count('game_logs');
                $renderData['totalWinGameCount']    = BaseModel::count('game_logs', ['status' => 1]);
                $renderData['totalLoseGameCount']   = BaseModel::count('game_logs', ['status' => 0]);
                $renderData['totalTaskSuccessSum']  = BaseModel::sum('game_logs', 'task_success');
                $renderData['totalTaskFailSum']     = BaseModel::sum('game_logs', 'task_fail');
                $renderData['totalLoseGameCount']   = BaseModel::count('game_logs', ['status' => 0]);
                $renderData['totalPaidWithdraw']    = BaseModel::count('withdraws', ['payment_status' => 1]);
                $renderData['totalWaitingWithdraw'] = BaseModel::count('withdraws', ['payment_status' => 0]);
                $renderData['totalSumEarn']         = BaseModel::sum('game_logs', 'earn', ['status' => 1]);
                $renderData['totalSumWithdraw']     = BaseModel::sum('withdraws', 'amount', ['payment_status' => 1]);
                
                /**
                 * Today Analytics
                 */
                $renderData['todayUsers'] = BaseModel::count('users', [
                    ['register_time', '>=', $dayStart],
                    ['register_time', '<=', $dayEnd]
                ]);
                $renderData['todayGameCount'] = BaseModel::count('game_logs', [
                    ['time', '>=', $dayStart],
                    ['time', '<=', $dayEnd]
                ]);
                $renderData['todayWinGameCount'] = BaseModel::count('game_logs', [
                    ['time', '>=', $dayStart],
                    ['time', '<=', $dayEnd],
                    ['status', '=', 1]
                ]);
                $renderData['todayLoseGameCount'] = BaseModel::count('game_logs', [
                    ['time', '>=', $dayStart],
                    ['time', '<=', $dayEnd],
                    ['status', '=', 0]
                ]);
                $renderData['todaySumEarn'] = BaseModel::sum('game_logs', 'earn', [
                    ['time', '>=', $dayStart],
                    ['time', '<=', $dayEnd],
                    ['status', '=', 1]
                ]);
                $renderData['todaySumWithdraw'] = BaseModel::sum('withdraws', 'amount', [
                    ['payment_time', '>=', $dayStart],
                    ['payment_time', '<=', $dayEnd],
                    ['payment_status', '=', 1]
                ]);
            }

            return $this->view->render($response, 'dashboard/index.html', $renderData);
        }

        public function logout($request, $response, $args)
        {
            if(Session::get('login')) {
                Session::destroy('login');
            }
            return Url::redirect('dashboard');
        }
    }

?>