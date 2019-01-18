<?php

namespace App\Controllers\Task;

use App\Models\Projects;
use EasyHQ\Base\BaseController;
use EasyHQ\Session;

class HomeController extends BaseController {

    public function index() {
        if (Session::exists('member')) {
            $member = Session::get('member');
            $projects = Projects::select('P')
                ->addFields([
                    'TIMESTAMPDIFF(DAY, NOW(), P.deadline)' => 'nb_day',
                    'P.id' => 'project_id',
                    'P.name',
                    'P.price',
                    'P.deadline',
                    'U.id' => 'id_user_leader',
                    'U.nickname' => 'nickname_user_leader',
                    'UC.id' => 'id_user_client',
                    'UC.nickname' => 'nickname_user_client'
                ])
                ->innerJoin('users', 'U')
                ->onJoin('U.id', 'P.id_leader')
                ->leftJoin('projects_users', 'PU')
                ->onJoin('PU.id_project', 'P.id')
                ->leftJoin('users', 'UC')
                ->onJoin('UC.id', 'P.id_client')
                ->where('id_client', $member['id'])
                ->orWhere('id_leader', $member['id'])
                ->orWhere('PU.id_users', $member['id'])
                ->groupBy('project_id')
                ->get();
            $this->set('projects', $projects);
        }

        $this->script('projects');
        $this->render('task/home', 'task.home.title');
    }



}
