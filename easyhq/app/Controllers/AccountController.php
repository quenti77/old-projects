<?php

namespace App\Controllers;

use App\Models\Users;
use EasyHQ\Base\BaseController;
use EasyHQ\Helper;
use EasyHQ\Router\Router;
use EasyHQ\Session;
use EasyHQ\Translate;
use Intervention\Image\ImageManager;

class AccountController extends BaseController {

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

    public function show($id, $name) {
        $users = Users::select()
            ->where('id', $id)
            ->andWhere('nickname', $name)
            ->orWhere('user_key', $name)->get(0, 1);

        if (empty($users)) {
            Router::redirect('error.error404');
        }
        $user = $users[0];
        if (empty($user->avatar)) {
            $user->avatar = 'default.jpg';
        }

        $this->set('user', $user);
        $this->set('can_update', Users::canUpdate($user));
        $this->render('user/account', 'account.show.title');
    }

    public function form($id, $name) {
        $user = $this->checkUser($id, $name);

        $this->set('user', $user);
        $this->render('user/update', 'account.update.title');
    }

    public function update($id, $name) {
        $user = $this->checkUser($id, $name);

        if (Session::get('csrf') != Helper::post('_csrf')) {
            Router::redirect('home.index');
        }

        $modified = false;

        if (Helper::post('firstname') != null) {
            $user->firstname = Helper::post('firstname');
            $modified = true;
        }

        if (Helper::post('lastname') != null) {
            $user->lastname = Helper::post('lastname');
            $modified = true;
        }

        $password = [
            Helper::post('last_password'),
            Helper::post('new_password'),
            Helper::post('confirm_password')
        ];
        if (password_verify($password[0], $user->password)) {
            if ($password[1] == $password[2]) {
                $user->password = password_hash($password[1], PASSWORD_BCRYPT);
                $modified = true;
            }
        }

        $result = (Helper::post('showName') != null);
        if($user->show_name != $result) {
            $user->show_name = ($result) ? 1 : 0;
            $modified = true;
        }

        $result = (Helper::post('deleteAvatar') != null);
        if ($result) {
            $user->avatar = '';
            $modified = true;
        }

        if (isset($_FILES['avatar']['tmp_name']) &&
            !empty($_FILES['avatar']['tmp_name']) && !$result) {

            $max_size = 10 * 1024 * 1024;
            if ($_FILES['avatar']['size'] > $max_size) {
                Session::setFlash('danger', '', Translate::get('account.error.form.size'));
                Router::redirect('account.form', ['id' => $user->id, 'name' => $user->nickname]);
            }

            $extensions_valides = ['jpg', 'jpeg', 'gif', 'png'];
            $extension_upload = strtolower(substr(strrchr($_FILES['avatar']['name'], '.'), 1));
            if (!in_array($extension_upload,$extensions_valides)) {
                Session::setFlash('danger', '', Translate::get('account.error.form.format'));
                Router::redirect('account.form', ['id' => $user->id, 'name' => $user->nickname]);
            }

            $old_avatar = 'public/img/avatar/'.$user->avatar;
            $name = time().'-'.$user->id.'-avatar.png';

            if (file_exists(__DIR__.'/../../'.$old_avatar)) {
                unlink(__DIR__.'/../../'.$old_avatar);
            }

            $manager = new ImageManager();
            $manager->make($_FILES['avatar']['tmp_name'])
                ->fit(128, 128)
                ->save('public/img/avatar/'.$name);

            $user->avatar = $name;
            $modified = true;
        }

        if ($modified) {
            $user->save();

            Users::sessionSet($user);
            Session::setFlash('success', '', Translate::get('account.success.modify'));
        }
        Router::redirect('account.form', ['id' => $user->id, 'name' => $user->nickname]);
    }

    public function verify($key) {
        //Users::redirectIf(true);

        $users = Users::select()
            ->where('mail_check', $key)
            ->andWhere('TIMESTAMPDIFF(MINUTE, mail_check_at, NOW())', '<', 30, false)
            ->get();

        if (!empty($users)) {
            $user = $users[0];
            $user->mail_check = '1';
            $user->save();
        }

        $users = Users::select()
            ->where('mail_check', '!=', '1')
            ->andWhere('TIMESTAMPDIFF(MINUTE, mail_check_at, NOW())', '>', 35, false)
            ->get();

        foreach($users as $user) {
            $user->delete();
        }

        Session::setFlash('success', '', Translate::get('account.success.register.mail'));
        Router::redirect('home.index');
    }

    public function reinit($id,$key){
        $this->checkReinit($id, $key);
        $this->render('user/renew_passwd', 'account.reinit.title');
    }

    public function reinit_form($id, $key) {
        $user = $this->checkReinit($id, $key);
        $newpassword=Helper::post('newpasswd');
        $verifnewpassword=Helper::post('verifnewpasswd');
        if ($verifnewpassword == $newpassword) {
            $user->password = password_hash($newpassword, PASSWORD_BCRYPT);
            $user->mail_check = '1';
            $user->passwd_reinit = 0;
            $user->save();
            Session::setFlash('success', '', Translate::get('account.success.newpasswd'));
            Router::redirect('home.index');
        }
    }

    private function checkReinit($id, $key) {
        $user=Users::select()
            ->where('passwd_reinit', 1)
            ->andWhere('mail_check', $key)
            ->andWhere('id', $id)
            ->andWhere('TIMESTAMPDIFF(MINUTE, mail_check_at, NOW())', '<', 10, false)
            ->get();

        if (empty($user)) {
            Session::setFlash('danger', '', Translate::get('account.error.reinit'));
            Router::redirect('user.signin');
        }
        $user = $user[0];

        return $user;
    }

}
