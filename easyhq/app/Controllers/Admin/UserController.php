<?php

namespace App\Controllers\Admin;

use App\Models\Groups;
use App\Models\Users;
use EasyHQ\Base\BaseController;
use EasyHQ\Base\BaseModel;
use EasyHQ\Helper;
use EasyHQ\Router\Router;
use EasyHQ\Session;

class UserController extends BaseController {

    const NUMBER_ITEM_PER_PAGE = 15;

    public function show() {
        $this->checker('show_admin');
        $this->script('users_admin');
        $this->render('admin/users', 'admin.user.title');
    }

    public function get() {
        $this->getUsers();
    }

    public function getPage($page) {
        $this->getUsers($page);
    }

    private function getUsers($page = 1) {
        $nb = self::NUMBER_ITEM_PER_PAGE;
        $users = Users::select('U')->addFields([
            'U.id' => 'id_user',
            'U.nickname',
            'U.mail',
            'U.mail_check',
            'G.name' => 'group_name'
        ])->innerJoin('groups', 'G')
            ->onJoin('G.id', '=', 'U.id_group', false)
            ->where('U.nickname', 'LIKE', '%'.Helper::post('research').'%')
            ->orWhere('U.mail', 'LIKE', '%'.Helper::post('research').'%')
            ->get(($page - 1) * $nb, $nb);

        $count = Users::select()->addFields(['COUNT(*)' => 'number', 'nickname' ])
                                ->where('nickname', 'LIKE', '%'.Helper::post('research').'%')
                                ->orWhere('mail', 'LIKE', '%'.Helper::post('research').'%')->get();
        $max_page = 1;
        if (!empty($count)) {
            $max_page = ceil($count[0]->number / $nb);
        }

        $this->set('users', $users);
        $this->set('max_page', $max_page);
        $this->render('admin/users_get');
    }

    public function ajaxShow($id) {
        if (!Groups::check('site', Groups::getAuth('site', 'show_admin'))) {
            return;
        }

        $user = Users::find('id', $id);
        $this->set('url', Router::url('admin:user.update', ['id' => $id]));
        $this->set('user', $user);
        $this->set('groups', Groups::findAll());
        $this->render('admin/users_spec');
    }

    public function update($id) {
        $this->checker('update_full_admin');

        if (Session::get('csrf') != Helper::post('_csrf')) {
            Router::redirect('home.index');
        }

        $user = Users::select()->where('id', $id)->get();
        if (empty($user)) {
            Router::redirect('home.index');
        }

        $modified = false;
        $user = $user[0];
        $nickname = Helper::post('nickname');
        if (!empty($nickname) && $nickname != $user->nickname) {
            $user->nickname = $nickname;
            $modified = true;
        }

        $firstname = Helper::post('firstname');
        if ($firstname != $user->firstname) {
            $user->firstname = $firstname;
            $modified = true;
        }

        $lastname = Helper::post('lastname');
        if ($lastname != $user->lastname) {
            $user->lastname = $lastname;
            $modified = true;
        }

        $id_group = Helper::post('group');
        if ($id_group != $user->id_group) {
            $user->id_group = $id_group;
            $modified = true;
        }

        $mail = Helper::post('mail');
        if (!empty($mail) && $mail != $user->mail) {
            $user->mail = $mail;
            $user->mail_check = sha1($user->user_key.'-----'.time());
            $user->mail_check_at = BaseModel::now();
            Users::sendMailCheck($user);

            $modified = true;
        }

        $result = (Helper::post('deleteAvatar') != null);
        if ($result) {
            $user->avatar = '';
            $modified = true;
        }

        if ($modified) {
            $user->save();
        }

        Router::redirect('admin:user.show');
    }

    public function resend($id) {
        $this->checker('update_full_admin');

        $user = Users::select()->where('id', $id)->get();
        if (empty($user)) {
            Router::redirect('home.index');
        }

        $user = $user[0];
        $user->mail_check = sha1($user->user_key.'-----'.time());
        $user->mail_check_at = BaseModel::now();
        $user->save();
        Users::sendMailCheck($user);

        Router::redirect('admin:user.show');
    }

    private function checker($name, $ajax = false) {
        if (!Groups::check('site', Groups::getAuth('site', $name))) {
            if ($ajax) {
                echo Translate::get('admin.user.error.rights.ajax');
            } else {
                Session::setFlash('danger', '', Translate::get('admin.user.error.rights.html'));
                Router::redirect('home.index');
            }
        }
    }

}
