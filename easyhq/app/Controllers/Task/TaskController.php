<?php

namespace App\Controllers\Task;

use App\Models\ProjectsUsers;
use App\Models\Tasks;
use App\Models\TasksUsers;
use App\Models\Users;
use EasyHQ\Base\BaseController;
use EasyHQ\Router\Router;
use EasyHQ\Session;
use EasyHQ\Translate;

class TaskController extends BaseController {

    public function detail($id) {
        Users::redirectIf(false);

        $member = Session::get('member');
        $this->checkUser($member['id']);

        $task = Tasks::select('T')
            ->addFields([
                'T.id' => 'id',
                'T.name' => 'name',
                'T.content' => 'content',
                'P.id' => 'id_project',
                'P.id_leader' => 'id_leader'
            ])
            ->innerJoin('projects', 'P')
            ->onJoin('P.id', '=', 'T.id_project')
            ->where('T.id', $id)->get();
        if (empty($task)) {
            Router::redirect('home.index');
        }

        $task = $task[0];
        $projects_members = ProjectsUsers::select('PU')
            ->innerJoin('users', 'U')
            ->onJoin('U.id', '=', 'PU.id_users')
            ->andJoin('U.id', '!=', $task->id_leader)
            ->where('PU.id_project', $task->id_project)
            ->get();

        if (empty($projects_members)) {
            Router::redirect('home.index');
        }

        $taskUsers = TasksUsers::select()->where('id_task', $id)->get();
        $tu = [];
        if (!empty($taskUsers)) {
            foreach ($taskUsers as $taskUser) {
                $tu[] = $taskUser->id_user;
            }
        }

        $this->set([
            'task' => $task,
            'users' => $tu,
            'members' => $projects_members,
            'connectedMember' => $member
        ]);
        $this->render('task/task_members', Translate::parse('task.task.title', [$id]));
    }

    public function toggleUser($idTask, $idUser, $csrf) {
        Users::redirectIf(false);

        $member = Session::get('member');
        $this->checkUser($member['id']);

        if ($csrf != Session::get('csrf')) {
            Router::redirect('home.index');
        }

        $tasks = Tasks::select('T')
            ->innerJoin('projects', 'P')
            ->onJoin('P.id', '=', 'T.id_project')
            ->where('T.id', $idTask)
            ->andWhere('P.id_leader', $member['id'])
            ->get();

        if (empty($tasks)) {
            Router::redirect('home.index');
        }

        $tasks = TasksUsers::select()
            ->where('id_task', $idTask)
            ->andWhere('id_user', $idUser)
            ->get();

        if (empty($tasks)) {
            $task = TasksUsers::create();

            $task->id_task = $idTask;
            $task->id_user = $idUser;
            $task->save();
            Session::setFlash('success', '', Translate::get('task.task.success.member.add'));
        } else {
            $task = $tasks[0];
            $task->delete();
            Session::setFlash('success', '', Translate::get('task.task.success.member.remove'));
        }

        Router::redirect('task:task.detail', ['id' => $idTask]);
    }

    private function checkUser($id_user) {
        if (ProjectsUsers::find('id_users', $id_user) === null) {
            Router::redirect('home.index');
        }
    }

}
