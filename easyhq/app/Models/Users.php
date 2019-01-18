<?php

namespace App\Models;

use EasyHQ\Base\BaseModel;
use EasyHQ\Config;
use EasyHQ\Mail;
use EasyHQ\Router\Router;
use EasyHQ\Session;
use EasyHQ\Translate;

class Users extends BaseModel {

    protected $class_name = __CLASS__;

    public $desc = [
        'id' => 0,
        'id_group' => \PDO::PARAM_INT,
        'nickname' => \PDO::PARAM_STR,
        'password' => \PDO::PARAM_STR,
        'passwd_reinit' => \PDO::PARAM_INT,
        'mail' => \PDO::PARAM_STR,
        'mail_check' => \PDO::PARAM_STR,
        'mail_check_at' => \PDO::PARAM_STR,
        'register_at' => \PDO::PARAM_STR,
        'connection_at' => \PDO::PARAM_STR,
        'user_key' => \PDO::PARAM_STR,
        'firstname' => \PDO::PARAM_STR,
        'lastname' => \PDO::PARAM_STR,
        'show_name' => \PDO::PARAM_INT,
        'avatar' => \PDO::PARAM_STR
    ];

    public function __construct() {
        parent::__construct(BaseModel::MODE_UPDATE);
    }

    public static function sessionSet($user) {
        Session::set('member', [
            'id' => $user->id,
            'id_group' => $user->id_group,
            'nickname' => $user->nickname,
            'user_key' => $user->user_key,
            'avatar' => (empty($user->avatar)) ? 'default.jpg' : $user->avatar
        ]);
    }

    public static function redirectIf($connected) {
        if (Session::exists('member') == $connected) {
            Router::redirect('home.index');
        }
    }

    public static function canUpdate($user) {
        $can_update = false;
        if (Session::exists('member')) {
            $member = Session::get('member');

            if ($member['nickname'] == $user->nickname &&
                $member['id'] == $user->id &&
                $member['user_key'] == $user->user_key) {
                $can_update = true;
            }
        }

        return $can_update;
    }

    public static function sendMailCheck($user) {
        $mail = new Mail();

        $from = Config::getField('MAIL_FROM');
        $from = [$from['mail'] => $from['name']];

        $to = explode('@', $user->mail);
        $to = [$user->mail => $to[0]];

        $mail->send($from, $to, 'TITLE', Translate::getMails(null, 'account/register', 'Register', [
            'key' => $user->mail_check
        ]));
    }

    public static function resendPassword($user) {
        $mail = new Mail();

        $from = Config::getField('MAIL_FROM');
        $from = [$from['mail'] => $from['name']];

        $to = explode('@', $user->mail);
        $to = [$user->mail => $to[0]];

        $mail->send($from, $to, 'TITLE', Translate::getMails(null, 'account/password', 'Mot de passe oublié', [
            'user' => $user
        ]));
    }

    public static function addFriend($id_from, $id_to) {
        $mail = new Mail();

        $user_name_sender = Users::find('id', $id_from);
        if (!$user_name_sender) {
            return;
        }
        $user_name_sender = $user_name_sender->nickname;

        $user = Users::find('id', $id_to);
        if (!$user) {
            return;
        }

        $from = Config::getField('MAIL_FROM');
        $from = [$from['mail'] => $from['name']];

        $to = explode('@', $user->mail);
        $to = [$user->mail => $to[0]];

        $mail->send($from, $to, 'TITLE', Translate::getMails(null, 'account/friend_add', 'Demande reçu', [
            'user_name_sender' => $user_name_sender
        ]));
    }

}
