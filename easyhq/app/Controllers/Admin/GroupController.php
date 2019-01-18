<?php

namespace App\Controllers\Admin;

use App\Models\Groups;
use App\Models\Users;
use EasyHQ\Base\BaseController;
use EasyHQ\BinaryRight;
use EasyHQ\Helper;
use EasyHQ\Router\Router;
use EasyHQ\Session;
use EasyHQ\Translate;

class GroupController extends BaseController {

    private function checker($name, $ajax = false) {
        if (!Groups::check('site', Groups::getAuth('site', $name))) {
            if ($ajax) {
                echo Translate::get('admin.group.error.rights.ajax');
            } else {
                Session::setFlash('danger', '', Translate::get('admin.group.error.rights.html'));
                Router::redirect('home.index');
            }
        }
    }

    private function calculBinaryRight($type) {
        $site = Helper::post($type);
        $result = 0;

        if ($site != null && is_array($site)) {
            foreach($site as $name => $value) {
                if (isset(Groups::$authorization[$type][$name])) {
                    $result |= Groups::$authorization[$type][$name];
                }
            }
        }

        return $result;
    }

    private function isActive($type, $name, $auth) {
        $authCheck = 0;
        $auth = intval($auth);
        if (isset(Groups::$authorization[$type][$name])) {
            $authCheck = Groups::$authorization[$type][$name];
        }

        $br = new BinaryRight($auth);
        return $br->compare($authCheck);
    }

    public function show() {
        $this->checker('show_admin');
        $this->set([
            'groups' => Groups::findAll(),
            'info' => Groups::$authorization
        ]);

        $this->script('groups_admin');
        $this->render('admin/groups', 'admin.group.title');
    }

    public function insert() {
        $this->checker('update_full_admin');

        if (Session::get('csrf') != Helper::post('_csrf')) {
            Router::redirect('home.index');
        }

        $name = Helper::post('name');
        $description = Helper::post('description');
        $group = Groups::select()->where('name', $name)->get();
        if (!empty($group) || empty($name) || empty($description)) {
            Router::redirect('home.index');
        }

        $group = Groups::create();
        $group->name = $name;
        $group->description = $description;
        $group->auth_site = ''.$this->calculBinaryRight('site');
        $group->auth_news = ''.$this->calculBinaryRight('news');
        $group->save();

        Router::redirect('admin:group.show');
    }

    public function update($id) {
        $this->checker('update_full_admin');

        if (Session::get('csrf') != Helper::post('_csrf')) {
            Router::redirect('home.index');
        }

        $group = Groups::select()->where('id', $id)->get();
        if (empty($group)) {
            Router::redirect('home.index');
        }

        $group = $group[0];
        $name = Helper::post('name');
        if (!empty($name) && $name != $group->name) {
            $group->name = $name;
        }

        $description = Helper::post('description');
        if (!empty($description) && $description != $group->description) {
            $group->description = $description;
        }

        $group->auth_site = ''.$this->calculBinaryRight('site');
        $group->auth_news = ''.$this->calculBinaryRight('news');

        $group->save();
        Router::redirect('admin:group.show');
    }

    public function defineDefault($id, $csrf) {
        $this->checker('update_full_admin');

        if (Session::get('csrf') != $csrf) {
            Router::redirect('home.index');
        }

        $group = Groups::select()->where('id', $id)->get();
        if (empty($group)) {
            Router::redirect('home.index');
        }
        $group = $group[0];
        $group->g_default = 1;
        $group->save();

        $other = Groups::select()->where('id', '!=', $id)->get();
        if (!empty($other)) {
            foreach($other as $g) {
                $g->g_default = 0;
                $g->save();
            }
        }

        Router::redirect('admin:group.show');
    }

    public function delete($id, $csrf) {
        $this->checker('update_full_admin');

        if (Session::get('csrf') != $csrf) {
            Router::redirect('home.index');
        }

        $group = Groups::select()->where('id', $id)->get();
        if (empty($group)) {
            Router::redirect('home.index');
        }

        $countGroup = Groups::select()->addFields(['COUNT(*)' => 'nb'])->get();
        $countGroup = intval($countGroup[0]->nb);

        if ($countGroup > 1) {
            $group = $group[0];
            $group->delete();

            $firstGroup = Groups::select()->addFields('id')->get(0, 1);
            $firstGroup = $firstGroup[0];

            $users = Users::select()->where('id_group', $id)->get();
            if (!empty($users)) {
                foreach($users as $user) {
                    $user->id_group = $firstGroup->id;
                    $user->save();
                }
            }
        } else {
            Session::setFlash('danger', '', Translate::get('admin.group.error.count'));
        }

        Router::redirect('admin:group.show');
    }

    public function ajaxInsert() {
        $this->ajaxSub();
    }

    public function ajaxShow($id) {
        $this->ajaxSub($id);
    }

    private function ajaxSub($id = 0) {
        if (!Groups::check('site', Groups::getAuth('site', 'show_admin'))) {
            return;
        }

        $group = Groups::findOrCreate('id', $id);
        $get = [];

        foreach(Groups::$authorization as $k => $v) {
            if (!isset($get[$k])) {
                $get[$k] = [];
            }

            $name = "auth_$k";
            foreach($v as $key => $value) {
                $get[$k][$key] = $this->isActive($k, $key, $group->$name);
            }
            $this->set($k, $get[$k]);
        }

        if ($group->id == 0) {
            $url = Router::url('admin:group.insert');
        } else {
            $url = Router::url('admin:group.update', ['id' => $group->id]);
        }

        $this->set([
            'group' => $group,
            'url' => $url
        ]);

        $this->render('admin/groups_spec');
    }

}
