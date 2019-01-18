<?php

namespace App\Models;

use EasyHQ\Base\BaseModel;
use EasyHQ\BinaryRight;
use EasyHQ\Session;

class Groups extends BaseModel {

    public static $authorization = [
        'site' => [
            'nothing_right'         => 0b00000000,
            'connection'            => 0b00000001,
            'update_our_profil'     => 0b00000010,
            'update_other_profil'   => 0b00000100,
            'show_admin'            => 0b00001000,
            'update_minimal_admin'  => 0b00010000,
            'update_full_admin'     => 0b00100000
        ],
        'news' => [
            'nothing_right'     => 0b00000000,
            'read'              => 0b00000001,
            'comment'           => 0b00000010,
            'write'             => 0b00000100,
            'update'            => 0b00001000,
            'full_update'       => 0b00010000
        ]
    ];

    protected $class_name = __CLASS__;

    public $desc = [
        'id' => 0,
        'name' => \PDO::PARAM_STR,
        'description' => \PDO::PARAM_STR,
        'g_default' => \PDO::PARAM_INT,
        'auth_site' => \PDO::PARAM_INT,
        'auth_news' => \PDO::PARAM_INT
    ];

    public function __construct() {
        parent::__construct(BaseModel::MODE_UPDATE);
    }

    public static function check($type, $auths) {
        $id_group = 0;
        if (Session::exists('member')) {
            $member = Session::get('member');
            $id_group = $member['id_group'];
        }

        $auth = self::$authorization[$type]['nothing_right'];
        if ($id_group != 0) {
            $group = Groups::select()->where('id', $id_group)->get();

            if (!empty($group)) {
                $field = "auth_".$type;
                $auth = $group[0]->$field;
            }
        }

        $br = new BinaryRight($auth);
        return $br->compare($auths);
    }

    public static function getAuth($type, $name) {
        if (isset(self::$authorization[$type][$name])) {
            return self::$authorization[$type][$name];
        }

        return 0;
    }

}
