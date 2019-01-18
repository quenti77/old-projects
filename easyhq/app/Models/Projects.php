<?php

namespace App\Models;

use EasyHQ\Base\BaseModel;
use EasyHQ\Query\Condition;

class Projects extends BaseModel {

    protected $class_name = __CLASS__;

    public $desc = [
        'id' => 0,
        'id_client' => \PDO::PARAM_INT,
        'id_leader' => \PDO::PARAM_INT,
        'name' => \PDO::PARAM_STR,
        'description' => \PDO::PARAM_STR,
        'price' => \PDO::PARAM_STR,
        'deadline' => \PDO::PARAM_STR
    ];

    public function __construct() {
        parent::__construct(BaseModel::MODE_UPDATE);
    }

    public static function getById($id, $member) {
        return Projects::select('P')
            ->addFields([
                'TIMESTAMPDIFF(DAY, NOW(), P.deadline)' => 'nb_day',
                'P.id' => 'project_id',
                'P.name' => 'project_name',
                'P.price' => 'project_price',
                'P.deadline' => 'project_deadline',
                'U.id' => 'project_id_leader',
                'UC.id' => 'client_id',
                'UC.nickname' => 'client_nickname'
            ])
            ->innerJoin('users', 'U')
            ->onJoin('U.id', 'P.id_leader')
            ->leftJoin('projects_users', 'PU')
            ->onJoin('PU.id_project', 'P.id')
            ->leftJoin('users', 'UC')
            ->onJoin('UC.id', 'P.id_client')
            ->where('P.id', $id)
            ->andGroup([
                new Condition('WHERE', '', 'id_client', '=', $member['id'], false),
                new Condition('WHERE', 'OR', 'id_leader', '=', $member['id'], false),
                new Condition('WHERE', 'OR', 'PU.id_users', '=', $member['id'], false),
            ])
            ->groupBy('project_id')
            ->get(0, 1);
    }



}
