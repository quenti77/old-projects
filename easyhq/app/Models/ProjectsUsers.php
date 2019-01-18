<?php

namespace App\Models;

use EasyHQ\Base\BaseModel;
use EasyHQ\Query\Condition;

class ProjectsUsers extends BaseModel {

    protected  $class_name = __CLASS__;
    protected $table = 'projects_users';

    public $desc = [
        'id_project' => \PDO::PARAM_INT,
        'id_users' => \PDO::PARAM_INT
    ];

    public function __construct() {
        parent::__construct(BaseModel::MODE_UPDATE);
    }

    public static function membersList($id){
        /*Get member list*/
        $members_list = ProjectsUsers::select('PU')
            ->addFields([
                'PU.id_project' => 'id_projects',
                'PU.id_users' => 'id_users',
                'U.nickname'
            ])
            ->innerJoin('projects','P')
            ->onJoin('P.id', '=','PU.id_project')
            ->innerJoin('users','U')
            ->onJoin('U.id','=','PU.id_users')
            ->where('P.id','=', $id)
            ->get();
        return $members_list;
    }
}