<?php

    namespace App\Controllers\Dashboard;

    use App\Controllers\BaseController;
    use App\Models\Dashboard\LevelModel;
    use App\System\Helpers\Url;

    class LevelController extends BaseController
    {
        public function index($request, $response, $args)
        {            
            // get levels
            $levels = LevelModel::all();

            // render page
            return $this->view->render($response, 'dashboard/levels/index.html', [
                'levels' => $levels
            ]);
        }

        public function add($request, $response, $args)
        {
            if($request->isPost()) {
                // get post body
                $body = $request->getParsedBody();

                // post body get data
                $level = $body['level'];
                $level_start_xp = $body['level_start_xp'];
                $level_end_xp = $body['level_end_xp'];
                $time = $body['time'];
                $task = $body['task'];
                $heart = $body['heart'];
                $heart_time = $body['heart_time'];
                $earn = $body['earn'];
                $referral_percent = $body['referral_percent'];
                $earn_xp = $body['earn_xp'];

                try {
                    // insert level
                    $lastID = LevelModel::insert([
                        'level' => $level,
                        'level_start_xp' => $level_start_xp,
                        'level_end_xp' => $level_end_xp,
                        'time' => $time,
                        'task' => $task,
                        'heart' => $heart,
                        'heart_time' => $heart_time,
                        'earn' => $earn,
                        'referral_percent' => $referral_percent,
                        'earn_xp' => $earn_xp
                    ]);
                    
                    // add flash message
                    $this->flash->addMessage('success', 'Əlavə edildi');
                    // redirect page
                    return Url::redirect('dashboard.levels.edit', ['id' => $lastID]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // add flash message
                    $this->flash->addMessage('danger', 'Database Error: ' . $e->getMessage());
                }
                // redirect page
                return Url::redirect('dashboard.levels.add');
            }
            
            // render page
            return $this->view->render($response, 'dashboard/levels/add.html', [
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
                    $level = $body['level'];
                    $level_start_xp = $body['level_start_xp'];
                    $level_end_xp = $body['level_end_xp'];
                    $time = $body['time'];
                    $task = $body['task'];
                    $heart = $body['heart'];
                    $heart_time = $body['heart_time'];
                    $earn = $body['earn'];
                    $referral_percent = $body['referral_percent'];
                    $earn_xp = $body['earn_xp'];

                    try {
                        // update level
                        LevelModel::update(['id' => $id], [
                            'level' => $level,
                            'level_start_xp' => $level_start_xp,
                            'level_end_xp' => $level_end_xp,
                            'time' => $time,
                            'task' => $task,
                            'heart' => $heart,
                            'heart_time' => $heart_time,
                            'earn' => $earn,
                            'referral_percent' => $referral_percent,
                            'earn_xp' => $earn_xp
                        ]);
                        
                        // add flash message
                        $this->flash->addMessage('success', 'Məlumatlar yeniləndi');
                    } catch (\Illuminate\Database\QueryException $e) {
                        // add flash message
                        $this->flash->addMessage('danger', 'Database Error: ' . $e->getMessage());
                    }
                    // redirect page
                    return Url::redirect('dashboard.levels.edit', ['id' => $id]);
                }

                // get information
                $info = LevelModel::info(['id' => $id]);
                if($info !== false) {
                    // render page
                    return $this->view->render($response, 'dashboard/levels/edit.html', [
                        'flash' => $this->flash->getMessages(),
                        'item'  => $info
                    ]);
                }
            }
            // redirect page
            return Url::redirect('dashboard.levels');
        }

        public function delete($request, $response, $args)
        {
            $id = @$args['id'];
            if($id > 0) {
                try {
                    // delete
                    LevelModel::delete(['id' => $id]);
                    // add flash message
                    $this->flash->addMessage('success', 'Level silindi');
                } catch (\Illuminate\Database\QueryException $e) {
                    // add flash message
                    $this->flash->addMessage('danger', 'Database Error: ' . $e->getMessage());
                }
            }
            // redirect
            return Url::redirect('dashboard.levels');
        }
    }

?>