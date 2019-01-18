<?php

namespace App\Controllers\Task;

use App\Models\Projects;
use App\Models\ProjectsUsers;
use App\Models\Tasks;
use App\Models\TasksUsers;
use App\Models\Users;
use EasyHQ\Base\BaseController;
use EasyHQ\Database;
use EasyHQ\Helper;
use EasyHQ\Query\Condition;
use EasyHQ\Router\Router;
use EasyHQ\Session;
use EasyHQ\Translate;

class ProjectController extends BaseController
{

    private function checkDate($date, $hour) {
        try {
            $deadline = new \DateTime($date.' '.$hour);
        } catch (\Exception $e) {
            Session::setFlash('danger', '', Translate::get('task.project.error.date'));
            Router::redirect('task:home.index');
        }

        $now = new \DateTime();
        if ($now > $deadline) {
            Session::setFlash('danger', '', Translate::get('task.project.error.date'));
            Router::redirect('task:home.index');
        }

        return $deadline->format('Y-m-d H:i:s');
    }

    public function detail($id) {
        Users::redirectIf(false);

        $member = Session::get('member');
        $project = Projects::getById($id, $member);

        if (empty($project)) {
            Session::setFlash('danger', '', Translate::get('task.project.error.empty'));
            Router::redirect('task:home.index');
        }
        $project = $project[0];

        $progress = Tasks::getProgress($project);
        if ($progress[0]->nb_finished === null) {
            $progress[0]->nb_finished = 0;
        }

        $name = 'danger';
        if ($progress[0]->progress >= 20.0 && $progress[0]->progress < 50.0) {
            $name = 'warning';
        } elseif ($progress[0]->progress >= 50.0) {
            $name = 'success';
        }

        $tasks = Tasks::select('T')
            ->addFields([
                'T.id' => 'task_id',
                'T.name' => 'task_name',
                'T.content'  => 'task_content',
                'T.deadline' => 'task_deadline',
                'T.complete' => 'task_complete'
            ])
            ->innerJoin('projects', 'P')
            ->onJoin('P.id', '=', 'T.id_project')
            ->innerJoin('task_users', 'task_users')
            ->onJoin('task_users.id_task', '=', 'T.id')
            ->andJoin('task_users.id_user', '=', $member['id'])
            ->orJoin('P.id_leader', '=', $member['id'])
            ->innerJoin('users', 'U')
            ->onJoin('U.id', '=', 'task_users.id_user')
            ->orJoin('U.id', '=', 'P.id_leader')
            ->where('P.id', $id)
            ->groupBy('T.id')
            ->get();

        $this->set([
            'project' => $project,
            'member' => $member,
            'progress' => [
                $progress[0]->progress,
                $name,
                $progress[0]->nb_finished,
                $progress[0]->nb_tasks
            ],
            'tasks' => $tasks
        ]);

        $this->script('tasks');
        $this->script('search_clients');
        $this->script('search_members');
        $this->render('task/detail', 'task.project.title');
    }



    public function insert() {
        Users::redirectIf(false);

        $member = Session::get('member');

        $name = Helper::post('name');
        $description = Helper::post('description');
        $price = intval(Helper::post('price'));
        $date = Helper::post('date');
        $hour = Helper::post('hour');

        if (empty($name) || empty($date) || empty($description) ||
            empty($hour) || $price < 0 || $price > 999999999.00) {
            Session::setFlash('danger', '', Translate::get('task.project.error.form.missing'));
            Router::redirect('task:home.index');
        }

        $deadline = $this->checkDate($date, $hour);
        $project = Projects::create();

        $project->id_leader = $member['id'];
        $project->id_client = 0;
        $project->name = $name;
        $project->description = $description;
        $project->price = $price;
        $project->deadline = $deadline;
        $project->save();

        $lastID = Database::get()->lastInsertId();

        // Ajout du leader dans la liste des projets
        $projectUsers = ProjectsUsers::create();
        $projectUsers->id_project = $lastID;
        $projectUsers->id_users = $member['id'];
        $projectUsers->save();

        Session::setFlash('success', '', Translate::get('task.project.success.add'));
        Router::redirect('task:home.index');
    }

    public function update($id) {
        Users::redirectIf(false);

        $member = Session::get('member');
        $project = Projects::select()
            ->where('id', $id)
            ->andWhere('id_leader', $member['id'])
            ->get(0, 1);

        if (empty($project)) {
            Session::setFlash('danger', '', Translate::get('task.project.error.empty'));
            Router::redirect('task:home.index');
        }
        $project = $project[0];
        $modified = false;

        $name = Helper::post('name');
        if (!empty($name) && $project->name != $name) {
            $project->name = $name;
            $modified = true;
        }

        $description = Helper::post('description');
        if (!empty($description) && $project->description != $description) {
            $project->description = $description;
            $modified = true;
        }

        $price = Helper::post('price');
        if (!empty($price) && $project->price != $price && $price < 999999999.00) {
            $project->price = $price;
            $modified = true;
        }

        $date = Helper::post('date');
        $hour = Helper::post('hour');
        $deadline = $project->deadline;
        if (!empty($date) && !empty($hour)) {
            try {
                $dl = new \DateTime($date.' '.$hour);

                $now = new \DateTime();
                if ($now < $dl) {
                    $deadline = $dl->format('Y-m-d H:i:s');
                }
            } catch (\Exception $e) {
                // Nothing do
            }
        } elseif (!empty($date) xor !empty($hour)) {
            Session::setFlash('warning', '', Translate::get('task.project.error.form.missing'));
        }

        if ($deadline != $project->deadline) {
            $project->deadline = $deadline;
            $modified = true;
        }

        if ($modified) {
            $project->save();

            Session::setFlash('success', '', Translate::get('task.project.success.update'));
        }
        Router::redirect('task:home.index');
    }

    public function delete($id) {
        Users::redirectIf(false);

        $member = Session::get('member');
        $project = Projects::find('id', $id);

        if ($project->id_leader != $member['id']) {
            Router::redirect('task:home.index');
        }

        // Suppression des utilisateurs possible du projet
        $projectsUsers = ProjectsUsers::select()->where('id_project', $project->id)->get();
        foreach($projectsUsers as $projectUsers) {
            $projectUsers->delete();
        }

        // Suppression des tâches associé au projet
        $tasks = Tasks::select()->where('id_project', $project->id)->get();
        foreach($tasks as $task) {
            $tasksUsers = TasksUsers::select()->where('id_task', $task->id)->get();

            // Suppression des utilisateurs associé au tâches du projet
            foreach($tasksUsers as $taskUsers) {
                $taskUsers->delete();
            }

            $task->delete();
        }

        $project->delete();
        Session::setFlash('success', '', Translate::get('task.project.success.delete'));
        Router::redirect('task:home.index');
    }

    public function ajaxInsert() {
        $this->ajaxSub();
    }

    public function ajaxUpdate($id) {
        $this->ajaxSub($id);
    }

    private function ajaxSub($id = 0) {
        Users::redirectIf(false);

        $member = Session::get('member');
        $project = Projects::findOrCreate('id', $id);

        if ($project->id == 0) {
            $dl = new \DateTime();
            $deadline = $dl->format('d/m/Y H:i');

            $url = Router::url('task:project.insert');
        } else {
            if ($project->id_leader != $member['id']) {
                return;
            }

            $dl = new \DateTime($project->deadline);
            $deadline = $dl->format('d/m/Y H:i');

            $url = Router::url('task:project.update', ['id' => $id]);
        }

        $this->set([
            'url' => $url,
            'deadline' => explode(' ', $deadline),
            'project' => $project,
            'memberList' => ProjectsUsers::membersList($project->id)
        ]);

        $this->render('task/project_spec');
    }

}