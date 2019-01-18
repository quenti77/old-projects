<?php

namespace App\Controllers;

use App\Models\Groups;
use App\Models\Users;
use EasyHQ\Base\BaseController;
use EasyHQ\Base\BaseModel;
use EasyHQ\BinaryRight;
use EasyHQ\Config;
use EasyHQ\Helper;
use EasyHQ\Mail;
use EasyHQ\Router\Router;
use EasyHQ\Session;
use EasyHQ\Translate;

class UserController extends BaseController {

    public function signIn() {
        Users::redirectIf(true);
        $this->render('user/login', 'user.login.title');
    }

    public function login() {
        Users::redirectIf(true);

        $nickname = Helper::post('nickname');
        $password = Helper::post('password');
        $remember_me = Helper::post('rememberme');

        if (empty($nickname) || empty($password)) {
            Session::setFlash('danger', '', Translate::get('user.error.form.missing'));
            Router::redirect('user.signin');
        }

        $users = Users::select()
            ->addFields(['id', 'id_group', 'password', 'nickname', 'mail', 'user_key', 'firstname', 'lastname', 'avatar'])
            ->where('nickname', $nickname)
            ->andWhere('mail_check', '1')
            ->orWhere('mail', $nickname)
            ->get(0, 1);

        if (empty($users) || !password_verify($password, $users[0]->password)) {
            Session::setFlash('danger', '', Translate::get('user.error.form.login'));
            Router::redirect('user.signin');
        }

        $user = $users[0];

        $group = Groups::select()->where('id', $user->id_group)->get();
        if (empty($group)) {
            Session::setFlash('danger', '', Translate::get('user.error.group.unknown'));
            Router::redirect('user.signin');
        }
        $group = $group[0];

        $br = new BinaryRight($group->auth_site);
        if (!$br->compare(Groups::getAuth('site', 'connection'))) {
            Session::setFlash('danger', '', Translate::get('user.error.group.ban'));
            Router::redirect('user.signin');
        }

        $user->connection_at = BaseModel::now();
        $user->passwd_reinit = 0;
        $user->save();
        Users::sessionSet($user);

        if ($remember_me !== null) {
            $memberInfo = ''.$user->id.'---'.sha1($user->nickname.$user->user_key.$user->password);
            setcookie('remember_me', $memberInfo, time()+(3600*24*7),'/',null,false,true);
        }

        Session::setFlash('success', '', Translate::get('user.success.login'));
        Router::redirect('home.index');
    }

    public function signUp() {
        Users::redirectIf(true);
        $this->render('user/register', 'user.register.title');
    }

    public function register() {
        Users::redirectIf(true);

        $nickname = Helper::post('nickname');
        $password = Helper::post('password');
        $confirm = Helper::post('password_confirm');
        $email = Helper::post('email');

        if (empty($nickname) || empty($password) || empty($confirm) || empty($email)) {
            Session::setFlash('danger', '', Translate::get('user.error.form.missing'));
            Router::redirect('user.signup');
        }

        $errors = [];

        if (strlen($nickname) < 3 || strlen($nickname) > 40) {
            $errors['username'] = Translate::get('user.error.form.username');
        }

        if (!preg_match('#[a-zA-Z0-9\_\.]+#', $nickname)) {
            $errors['username'] = Translate::get('user.error.form.username');
        }

        if (strlen($password) < 6) {
            $errors['password'] = Translate::get('user.error.form.password');
        }

        if ($password != $confirm) {
            $errors['password'] = Translate::get('user.error.form.password_confirm');
        }

        if (!preg_match('/[a-zA-Z0-9\_\-\.]{3,}@[a-zA-Z0-9\-]{2,}\.[a-z]{2,6}/', $email)) {
            $errors['email'] = Translate::get('user.error.form.email');
        }

        if (!empty($errors)) {
            ob_start();
            Translate::getContent('error_fields', ['errors' => $errors]);
            $content = ob_get_clean();

            Session::setFlash('danger', '', $content);
            Router::redirect('user.signup');
        }

        $users = Users::select()
            ->addFields(['id'])
            ->where('nickname', $nickname)
            ->andWhere('mail', $email)
            ->get(0, 1);

        if (!empty($users)) {
            Session::setFlash('danger', '', Translate::get('user.error.register'));
            Router::redirect('user.signup');
        }

        $random = '';
        while ($random == '') {
            $random = Config::randomString(14);

            if (Users::find('user_key', $random)) {
                $random = '';
            }
        }

        $default_group = Groups::find('g_default', 1);

        $user = Users::create();
        $user->id_group = $default_group->id; // TODO: Change for default group
        $user->nickname = $nickname;
        $user->password = password_hash($password, PASSWORD_BCRYPT);
        $user->user_key = $random;
        $user->mail = $email;
        $user->mail_check = sha1($user->user_key);
        $user->mail_check_at = BaseModel::now();
        $user->register_at = BaseModel::now();
        $user->connection_at = '0000-00-00 00:00:00';
        $user->save();

        Users::sendMailCheck($user);

        Session::setFlash('success', '', Translate::get('user.success.register'));
        Router::redirect('home.index');
    }

    public function logout() {
        Session::reset();
        Session::start();
        setcookie('remember_me','',time()-1,'/',null,false,true);
        Router::redirect('home.index');
    }

    public function forget() {
        Users::redirectIf(true);
        $this->render('user/forget', 'user.forget.title');
    }

    public function newPassword() {
        Users::redirectIf(true);

        try {
            $user = Users::findOrFail('mail', Helper::post('mail'));

            $user->passwd_reinit = 1;

            $hashUserKey = hash('sha256', $user->user_key);
            $hashUniqKey = hash('sha256', time().Config::randomString(15));

            $user->mail_check = hash('sha256', $hashUserKey.'_'.$hashUniqKey);
            $user->mail_check_at = BaseModel::now();
            $user->save();

            Users::resendPassword($user);
        } catch(\Exception $e) {
            Session::setFlash('danger', '', Translate::get('user.error.mail.unknown'));
        }
        Router::redirect('user.signin');
    }

}
