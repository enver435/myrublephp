<?php

    namespace App\Controllers\Dashboard;
    use App\Controllers\BaseController;
    use App\Models\Dashboard\PrizeRefModel;
    use App\System\Helpers\Url;

    class PrizeRefController extends BaseController
    {
        public function index($request, $response, $args)
        {            
            // get prizes
            $prizes = PrizeRefModel::all();

            // render page
            return $this->view->render($response, 'dashboard/prize-ref/index.html', [
                'prizes' => $prizes
            ]);
        }

        public function add($request, $response, $args)
        {
            if($request->isPost()) {
                // get post body
                $body = $request->getParsedBody();

                // post body get data
                $start_time = strtotime($body['start_time']);
                $end_time = strtotime($body['end_time']);
                $amount = $body['amount'];
                $status = $body['status'];

                try {
                    // insert level
                    $lastID = PrizeRefModel::insert([
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        'amount' => $amount,
                        'status' => $status,
                        'last_update_time' => time()
                    ]);
                    
                    // add flash message
                    $this->flash->addMessage('success', 'Əlavə edildi');
                    // redirect page
                    return Url::redirect('dashboard.prizeref.edit', ['id' => $lastID]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // add flash message
                    $this->flash->addMessage('danger', 'Database Error: ' . $e->getMessage());
                }
                // redirect page
                return Url::redirect('dashboard.prizeref.add');
            }
            
            // render page
            return $this->view->render($response, 'dashboard/prize-ref/add.html', [
                'flash' => $this->flash->getMessages()
            ]);
        }

        public function edit($request, $response, $args)
        {
            $id = $args['id'];
            if($id > 0) {
                if($request->isPost()) {
                    // get post body
                    $body = $request->getParsedBody();

                    // post body get data
                    $start_time = strtotime($body['start_time']);
                    $end_time = strtotime($body['end_time']);
                    $amount = $body['amount'];
                    $status = $body['status'];

                    try {
                        // update level
                        PrizeRefModel::update(['id' => $id], [
                            'start_time' => $start_time,
                            'end_time' => $end_time,
                            'amount' => $amount,
                            'status' => $status,
                            'last_update_time' => time()
                        ]);
                        
                        // add flash message
                        $this->flash->addMessage('success', 'Məlumatlar yeniləndi');
                    } catch (\Illuminate\Database\QueryException $e) {
                        // add flash message
                        $this->flash->addMessage('danger', 'Database Error: ' . $e->getMessage());
                    }
                    // redirect page
                    return Url::redirect('dashboard.prizeref.edit', ['id' => $id]);
                }

                // get information
                $info = PrizeRefModel::info(['id' => $id]);
                if($info !== false) {
                    // render page
                    return $this->view->render($response, 'dashboard/prize-ref/edit.html', [
                        'flash' => $this->flash->getMessages(),
                        'item'  => $info
                    ]);
                }
            }
            // redirect page
            return Url::redirect('dashboard.prizeref');
        }

        public function delete($request, $response, $args)
        {
            $id = @$args['id'];
            if($id > 0) {
                try {
                    // delete
                    PrizeRefModel::delete(['id' => $id]);
                    // add flash message
                    $this->flash->addMessage('success', 'Level silindi');
                } catch (\Illuminate\Database\QueryException $e) {
                    // add flash message
                    $this->flash->addMessage('danger', 'Database Error: ' . $e->getMessage());
                }
            }
            // redirect
            return Url::redirect('dashboard.prizeref');
        }
    }

?>