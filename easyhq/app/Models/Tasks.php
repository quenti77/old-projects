<?php

namespace App\Models;

use EasyHQ\Base\BaseModel;

class Tasks extends BaseModel {

    protected $class_name = __CLASS__;

    public $desc = [
        'id' => 0,
        'id_task_type' => \PDO::PARAM_INT,
        'id_project' => \PDO::PARAM_INT,
        'name' => \PDO::PARAM_STR,
        'content' => \PDO::PARAM_STR,
        'deadline' => \PDO::PARAM_STR,
        'complete' => \PDO::PARAM_INT
    ];

    public function __construct() {
        parent::__construct(BaseModel::MODE_UPDATE);
    }

    public static function getProgress($project) {
        return Tasks::select()
            ->addFields([
                'AVG(complete) * 100' => 'progress',
                'COUNT(complete)' => 'nb_tasks',
                'SUM(complete)' => 'nb_finished'
            ])
            ->where('id_project', $project->project_id)
            ->get();
    }

}
