<?php

namespace App\Models;

use EasyHQ\Base\BaseModel;

class UsersBook extends BaseModel {

    protected $class_name = __CLASS__;
    protected $table = 'users_book';

    public $desc = [
        'id_user_from' => \PDO::PARAM_INT,
        'id_user_to' => \PDO::PARAM_INT,
        'status' => \PDO::PARAM_INT
    ];

    public function __construct() {
        parent::__construct(BaseModel::MODE_UPDATE);
    }

}
