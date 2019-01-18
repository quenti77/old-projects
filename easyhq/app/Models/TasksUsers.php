<?php

namespace App\Models;

use EasyHQ\Base\BaseModel;
use EasyHQ\Query\Condition;

class TasksUsers extends BaseModel {

    protected  $class_name = __CLASS__;
    protected $table = 'task_users';

    public $desc = [
        'id_task' => \PDO::PARAM_INT,
        'id_user' => \PDO::PARAM_INT
    ];

    public function __construct() {
        parent::__construct(BaseModel::MODE_UPDATE);
    }

}