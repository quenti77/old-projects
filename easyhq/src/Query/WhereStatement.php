<?php

namespace EasyHQ\Query;

class WhereStatement {

    private $where = [];

    public function addWhere($key, $col, $op, $val, $escape) {
        $this->where[] = new Condition('WHERE', $key, $col, $op, $val, $escape);
    }

    public function get() {
        $query = '';

        if (!empty($this->where)) {
            foreach($this->where as $cond) {
                $query .= ' ' . $cond->get();
            }
        }

        return $query;
    }

}
