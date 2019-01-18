<?php

namespace App\Controllers\Task;

use App\Models\Projects;
use App\Models\ProjectsUsers;
use App\Models\Tasks;
use App\Models\TasksUsers;
use App\Models\Users;
use App\Models\UsersBook;
use EasyHQ\Base\BaseController;
use EasyHQ\Database;
use EasyHQ\Helper;
use EasyHQ\Query\Condition;
use EasyHQ\Router\Router;
use EasyHQ\Session;
use EasyHQ\Translate;

class DetailsController extends BaseController {

    const NUMBER_ITEM_PER_PAGE = 5;

    private function checkDate($date, $hour) {
        try {
            $deadline = new \DateTime($date.' '.$hour);
        } catch (\Exception $e) {
            Session::setFlash('danger', '', Translate::get('task.details.error.date'));
            Router::redirect('task:home.index');
        }

        $now = new \DateTime();
        if ($now > $deadline) {
            Session::setFlash('danger', '', Translate::get('task.details.error.date'));
            Router::redirect('task:home.index');
        }

        return $deadline->format('Y-m-d H:i:s');
    }

    public function insertTask(){
        //Fonction d'insertion de tâches dans la DB
        Users::redirectIf(false);
        $name = Helper::post('name');
        $project_id= Helper::post('project_id');
        $content = Helper::post('content');
        $date = Helper::post('date');
        $hour = Helper::post('hour');
        $workers = Helper::post('workers');
        if (empty($name) || empty($content) ){
            Session::setFlash('danger', '', Translate::get('task.details.error.form.missing'));
            Router::redirect('task:project.detail', ['id' => $project_id]);
        }

        $deadline = $this->checkDate($date, $hour);
        $project=Projects::select()->where('id', $project_id)->get();

        if (empty($project)) {
            Session::setFlash('danger', '', Translate::get('task.details.project.unknown'));
            Router::redirect('task:home.index');
        }
        $project = $project[0];

        if (!empty($date) && !empty($hour)) {
            try {
                $dl = new \DateTime($date.' '.$hour);

                $now = new \DateTime();
                $endDate = new \DateTime($project->deadline);
                if ($now < $dl && $dl < $endDate) {
                    $deadline = $dl->format('Y-m-d H:i:s');
                } else {
                    $message = Translate::get('task.details.error.date.deadline');
                    Session::setFlash('danger','', $message);
                    Router::redirect('task:project.detail', ['id' => $project_id]);
                }
            } catch (\Exception $e) {
                // Something will happen
                Session::setFlash('danger','', Translate::get('task.details.error.date'));
                Router::redirect('task:project.detail', ['id' => $project_id]);
            }
        } elseif (!empty($date) xor !empty($hour)) {
            Session::setFlash('warning', '', Translate::get('task.details.error.date.missing'));
        }

        /*Creation de la requete d'insertion de la tache*/
        $tasks = Tasks::create();
        $tasks->id_task_type = 0;
        $tasks->id_project = $project_id;
        $tasks->name = $name;
        $tasks->content = $content;

        if (!empty($deadline)) {
            $tasks->deadline = $deadline;
        }

        $tasks->save();
        /*Get last id pour pouvoir mettre à jour les assignations de personnes à la bonne tâche*/
        $lastId = Database::get()->lastInsertId();

        $this->assignTask($lastId,$workers);

        Session::setFlash('success', '', Translate::get('task.details.success.new'));
        Router::redirect('task:project.detail', ['id' => $project_id]);
    }

    public function updateTask($id_task){
        Users::redirectIf(false);

        /*  Selection de la tâche   */
        $task=Tasks::select()->where('id', $id_task)->get();
        if (empty($task)) {
            Router::redirect('task:home.index');
        }

        /*  Récupération des valeurs du formulaire  */
        $name=Helper::post('name');
        $content=Helper::post('content');
        $modified = false;
        $tasks = $task[0];

        /*  Affectation des valeurs récupérées précédemment dans les champs de la table*/
        if (!empty($name) && $name != $tasks->name) {
            $tasks->name=$name;
            $modified = true;
        }

        if (!empty($content) && $content != $tasks->content) {
            $tasks->content=$content;
            $modified = true;
        }

        $date = Helper::post('date');
        $hour = Helper::post('hour');
        $deadline = $tasks->deadline;
        $project=Projects::select()->where('id', $tasks->id_project)->get();
        if (!empty($date) && !empty($hour)) {
            try {
                $dl = new \DateTime($date.' '.$hour);

                $now = new \DateTime();
                $endDate = new \DateTime($project[0]->deadline);
                if ($now < $dl && $dl < $endDate) {
                    $deadline = $dl->format('Y-m-d H:i:s');
                } else {
                    $message = Translate::get('task.details.error.date.deadline');
                    Session::setFlash('danger','', $message);
                    Router::redirect('task:project.detail', ['id' => $tasks->id_project]);
                }
            } catch (\Exception $e) {
                // Something will happen
                Session::setFlash('danger','',Translate::get('task.details.error.date'));
                Router::redirect('task:project.detail', ['id' => $project[0]->id]);
            }
        } elseif (!empty($date) xor !empty($hour)) {
            Session::setFlash('warning', '', Translate::get('task.details.error.date.missing'));
        }

        if ($deadline != $tasks->deadline) {
            $tasks->deadline = $deadline;
            $modified = true;
        }

        if ($modified) {
            $tasks->save();
        }

        Session::setFlash('success', '', Translate::get('task.details.success.update'));
        Router::redirect('task:project.detail',['id' => $tasks->id_project]);
    }
    public function deleteTask($id_project,$id_task,$csrf){
        Users::redirectIf(false);

        if (Session::get('csrf') != $csrf) {
            Router::url('home.index');
        }

        /* Recupération de l'id du projet  */
        $project=Projects::find('id',$id_project);
        /*  Selection de la tâche à supprimée   */
        $task=Tasks::select()->where('id_project',$project->id)->andWhere('id',$id_task)->get();
        /*  Selection des membres assignés à la tâche   */
        $taskUser=TasksUsers::select()->where('id_task',$task[0]->id)->get();
        /*  Suppression des collaborateurs assignés à la tâche  */
        foreach($taskUser as $users) {
            $users->delete();
        }
        /*  Suppression de la tâche */
        $task[0]->delete();
        Session::setFlash('success','', Translate::get('task.details.success.delete'));
        Router::redirect('task:project.detail', ['id' => $id_project]);
    }

    public function ajaxInsert(){
        $this->createTask();
    }
    public function ajaxUpdate($id){
        $this->createTask($id);
    }
    public function createTask($id = 0){
        Users::redirectIf(false);
        $tasks = Tasks::findOrCreate('id',$id);
        $member = Session::get('member');

        if ($tasks->id == 0) {
            $dl = new \DateTime();
            $url = Router::url('task:details.inserttask');
        }else{
            $dl = new \DateTime($tasks->deadline);
            $url = Router::url('task:details.updatetask',['id'=>$tasks->id]);

            $project = Projects::find('id', $tasks->id_project);

            if ($project !== null && $project->id_leader != $member['id']) {
                return;
            }
        }
        $deadline = $dl->format('d/m/Y H:i');
        $this->set([
            'url' => $url,
            'deadline' => explode(' ', $deadline),
            'tasks' => $tasks,
            'project_id' =>Helper::post('id_project'),
            'leader_id' => Helper::post('leader_id'),
            'workers' => ProjectsUsers::memberslist(Helper::post('id_project'))
        ]);
        $this->render('task/task_spec');
    }
    private function checkUser($id, $name) {
        Users::redirectIf(false);
        $users = Users::select()
            ->where('id', $id)
            ->andWhere('nickname', $name)
            ->orWhere('user_key', $name)->get(0, 1);

        if (empty($users)) {
            Router::redirect('error.error404');
        }
        $user = $users[0];

        if (!Users::canUpdate($user)) {
            Router::redirect('home.index');
        }

        return $user;
    }

    public function changeClient($idProject, $id_from, $id_to, $csrf) {
        if (Session::get('csrf') != $csrf) {
            Router::url('home.index');
        }

        if (!Session::exists('member')) {
            Router::redirect('home.index');
        }

        $member = Session::get('member');
        $other = $id_from;

        if ($member['id'] == $other) {
            $other = $id_to;
        }

        $project = Projects::select()
            ->where('id', $idProject)
            ->andWhere('id_leader', $member['id'])
            ->get(0, 1);

        if (empty($project)) {
            Router::url('home.index');
        }

        $project = $project[0];
        if ($project->id_leader != $member['id']) {
            Router::url('home.index');
        }

        $project->id_client = intval($other);
        $project->save();

        Session::setFlash('success', '', Translate::get('task.detail.success.client.new'));
        Router::redirect('task:project.detail', ['id' => $idProject]);
    }

    private function assignTask($id_task, $id_users){
        /* Création de la requete d'assignation des personne à une tache */
        foreach($id_users as $id) {
            $assign = TasksUsers::create();

            $assign->id_task = $id_task;
            $assign->id_user = $id;
            $assign->save();
        }
    }

    public function isDone(){
        Users::redirectIf(false);
        $member = Session::get('member');

        $id = Helper::post('id');
        $id_project = Helper::post('id_p');
        $complete = Helper::post('complete');
        $success = false;

        if (!empty($id) && !empty($complete)) {

            $select_task=Tasks::select()
                ->where('id', $id)
                ->get();
            if (!empty($select_task)){
                $select_task = $select_task[0];
                $select_task->complete=$complete;
                $select_task->save();
                $success = true;
            }
        }

        $project = Projects::getById($id_project, $member);

        $progress = false;
        if (!empty($project)) {
            $project = $project[0];
            $progress = Tasks::getProgress($project);
        }

        echo json_encode([
            'success' => $success,
            'progress' => $progress
        ]);
    }

    // Get all contacts by page
    public function get($project_id) {
        $this->getBooks($project_id);
    }

    public function getPage($project_id, $page) {
        $this->getBooks($project_id, $page);
    }

    private function getBooks($project_id, $page = 1) {
        if (!Session::exists('member')) {
            Router::redirect('home.index');
        }

        $nb = self::NUMBER_ITEM_PER_PAGE;
        $member = Session::get('member');
        $user = $this->checkUser($member['id'], $member['nickname']);
        $search = Helper::post('research');

        $project = Projects::select()
            ->where('id', $project_id)
            ->andWhere('id_leader', $member['id'])
            ->get(0, 1);

        if (empty($project)) {
            Router::url('home.index');
        }

        $project = $project[0];

        $book = UsersBook::select()
            ->addFields([
                'users_book.status' => 'status',
                'F.id' => 'id_from',
                'F.nickname' => 'nickname_from',
                'T.id' => 'id_to',
                'T.nickname' => 'nickname_to'
            ])
            ->innerJoin('users', 'F')
            ->onJoin('F.id', '=', 'users_book.id_user_from')
            ->innerJoin('users', 'T')
            ->onJoin('T.id', '=', 'users_book.id_user_to')
            ->where('status', 2)
            ->andGroup([
                new Condition('WHERE', '', 'users_book.id_user_from', '!=', $project->id_client, false),
                new Condition('WHERE', 'AND', 'users_book.id_user_to', '!=', $project->id_client, false)
            ])
            ->andGroup([
                new Condition('WHERE', '', 'F.nickname', 'LIKE', '%'.$search.'%', false),
                new Condition('WHERE', 'OR', 'F.mail', 'LIKE', '%'.$search.'%', false),
                new Condition('WHERE', 'OR', 'T.nickname', 'LIKE', '%'.$search.'%', false),
                new Condition('WHERE', 'OR', 'T.mail', 'LIKE', '%'.$search.'%', false)
            ])
            ->andGroup([
                new Condition('WHERE', '', 'id_user_from', '=', $member['id'], false),
                new Condition('WHERE', 'OR', 'id_user_to', '=', $member['id'], false)
            ])
            ->get(($page - 1) * $nb, $nb);

        $count = UsersBook::select()->addFields(['COUNT(*)' => 'nb_row'])
            ->innerJoin('users', 'F')
            ->onJoin('F.id', '=', 'users_book.id_user_from')
            ->innerJoin('users', 'T')
            ->onJoin('T.id', '=', 'users_book.id_user_to')
            ->where('status', 2)
            ->andGroup([
                new Condition('WHERE', '', 'F.nickname', 'LIKE', '%'.$search.'%', false),
                new Condition('WHERE', 'OR', 'F.mail', 'LIKE', '%'.$search.'%', false),
                new Condition('WHERE', 'OR', 'T.nickname', 'LIKE', '%'.$search.'%', false),
                new Condition('WHERE', 'OR', 'T.mail', 'LIKE', '%'.$search.'%', false)
            ])
            ->andGroup([
                new Condition('WHERE', '', 'id_user_from', '=', $member['id'], false),
                new Condition('WHERE', 'OR', 'id_user_to', '=', $member['id'], false)
            ])
            ->get(0, 1)[0];

        $max_page = ceil($count->nb_row / $nb);
        if ($max_page == 0) {
            $max_page = 1;
        }

        $this->set('books', $book);
        $this->set('max_page', $max_page);
        $this->set('project', $project);
        $this->set('user', $user);
        $this->render('task/detail_user');
    }

    // Get all contacts by page
    public function getMembers($project_id) {
        $this->getMembersAjax($project_id);
    }

    public function getMembersPage($project_id, $page) {
        $this->getMembersAjax($project_id, $page);
    }

    private function getMembersAjax($project_id, $page = 1) {
        if (!Session::exists('member')) {
            Router::redirect('home.index');
        }

        $nb = self::NUMBER_ITEM_PER_PAGE;
        $member = Session::get('member');
        $user = $this->checkUser($member['id'], $member['nickname']);
        $search = Helper::post('research');

        $project = Projects::select()
            ->where('id', $project_id)
            ->andWhere('id_leader', $member['id'])
            ->get(0, 1);

        if (empty($project)) {
            Router::url('home.index');
        }

        $project = $project[0];

        $projects_members = ProjectsUsers::select()->where('id_project', $project->id)->get();
        $pm = [];
        if (!empty($projects_members)) {
            foreach ($projects_members as $projects_member) {
                $pm[] = $projects_member->id_users;
            }
        }

        $book = UsersBook::select()
            ->addFields([
                'users_book.status' => 'status',
                'F.id' => 'id_from',
                'F.nickname' => 'nickname_from',
                'T.id' => 'id_to',
                'T.nickname' => 'nickname_to'
            ])
            ->innerJoin('users', 'F')
            ->onJoin('F.id', '=', 'users_book.id_user_from')
            ->innerJoin('users', 'T')
            ->onJoin('T.id', '=', 'users_book.id_user_to')
            ->where('status', 2)
            ->andGroup([
                new Condition('WHERE', '', 'users_book.id_user_from', '!=', $project->id_client, false),
                new Condition('WHERE', 'AND', 'users_book.id_user_to', '!=', $project->id_client, false)
            ])
            ->andGroup([
                new Condition('WHERE', '', 'F.nickname', 'LIKE', '%'.$search.'%', false),
                new Condition('WHERE', 'OR', 'F.mail', 'LIKE', '%'.$search.'%', false),
                new Condition('WHERE', 'OR', 'T.nickname', 'LIKE', '%'.$search.'%', false),
                new Condition('WHERE', 'OR', 'T.mail', 'LIKE', '%'.$search.'%', false)
            ])
            ->andGroup([
                new Condition('WHERE', '', 'id_user_from', '=', $member['id'], false),
                new Condition('WHERE', 'OR', 'id_user_to', '=', $member['id'], false)
            ])
            ->get(($page - 1) * $nb, $nb);

        $count = UsersBook::select()->addFields(['COUNT(*)' => 'nb_row'])
            ->innerJoin('users', 'F')
            ->onJoin('F.id', '=', 'users_book.id_user_from')
            ->innerJoin('users', 'T')
            ->onJoin('T.id', '=', 'users_book.id_user_to')
            ->where('status', 2)
            ->andGroup([
                new Condition('WHERE', '', 'F.nickname', 'LIKE', '%'.$search.'%', false),
                new Condition('WHERE', 'OR', 'F.mail', 'LIKE', '%'.$search.'%', false),
                new Condition('WHERE', 'OR', 'T.nickname', 'LIKE', '%'.$search.'%', false),
                new Condition('WHERE', 'OR', 'T.mail', 'LIKE', '%'.$search.'%', false)
            ])
            ->andGroup([
                new Condition('WHERE', '', 'id_user_from', '=', $member['id'], false),
                new Condition('WHERE', 'OR', 'id_user_to', '=', $member['id'], false)
            ])
            ->get(0, 1)[0];

        $max_page = ceil($count->nb_row / $nb);
        if ($max_page == 0) {
            $max_page = 1;
        }

        $this->set('books', $book);
        $this->set('max_page', $max_page);
        $this->set('project', $project);
        $this->set('user', $user);
        $this->set('projectsMembers', $pm);
        $this->render('task/detail_members');
    }

    public function changeMember($idProject, $idUser, $csrf) {
        if (!Session::exists('member')) {
            Router::redirect('home.index');
        }

        if ($csrf != Session::get('csrf')) {
            Router::redirect('home.index');
        }

        $projectMember = ProjectsUsers::select()
            ->where('id_project', $idProject)->andWhere('id_users', $idUser)->get();

        if (!empty($projectMember)) {
            $projectMember[0]->delete();
            Session::setFlash('success', '', Translate::get('task.detail.success.member.remove'));
        } else {
            $projectMember = ProjectsUsers::create();

            $projectMember->id_project = $idProject;
            $projectMember->id_users = $idUser;
            $projectMember->save();

            Session::setFlash('success', '', Translate::get('task.detail.success.member.add'));
        }

        Router::redirect('task:project.detail', ['id' => $idProject]);
    }

}
