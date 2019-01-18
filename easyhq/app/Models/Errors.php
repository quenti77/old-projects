<?php

namespace App\Models;

use EasyHQ\Base\BaseModel;

class Errors extends BaseModel {

    protected $class_name = __CLASS__;

    public $desc = [
        'id' => 0,
        'number' => \PDO::PARAM_INT,
        'message' => \PDO::PARAM_STR,
        'file' => \PDO::PARAM_STR,
        'line' => \PDO::PARAM_INT,
        'time_catch' => \PDO::PARAM_STR
    ];

    public function __construct() {
        parent::__construct(BaseModel::MODE_UPDATE);
    }

}
