<?php

namespace EasyHQ\Query;

class JoinStatement {

    private $type;
    private $table;
    private $alias;
    private $conditions = [];

    public function __construct($type, $table, $alias) {
        $this->type = $type;
        $this->table = $table;
        $this->alias = $alias;
    }

    public function addCondition($type, $col, $op, $val, $escape) {
        if (empty($this->conditions)) {
            $type = 'start';
        }

        $this->conditions[] = new Condition('ON', $type, $col, $op, $val, $escape);
    }

    public function get() {
        $query = '';

        switch ($this->type) {
            case 'left':
                $query = ' LEFT JOIN ';
                break;
            case 'right':
                $query = ' RIGHT JOIN ';
                break;
            default:
                $query = ' INNER JOIN ';
                break;
        }

        $query .= $this->table.' AS '.$this->alias;

        if (empty($this->conditions)) {
            $query .= ' ON 1=1';
        } else {
            foreach($this->conditions as $cond) {
                $query .= ' '.$cond->get();
            }
        }

        return $query;
    }

}
