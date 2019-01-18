<?php

namespace EasyHQ\Query;

use EasyHQ\Database;

class Condition {

    private $start;
    private $type;
    private $col;
    private $op;
    private $val;

    public function __construct($start, $type, $col, $operator, $val, $escape) {
        $this->start = $start;

        if ($val === null) {
            $val = $operator;
            $operator = '=';
        }

        $op = ['=', '<', '<=', '>', '>=', '!=', 'LIKE', 'IN', 'NOT IN'];
        $op_no_quote = ['LIKE', 'IN', 'NOT IN'];
        if (!in_array($operator, $op)) {
            $operator = '=';
        }

        if ($escape && !in_array($operator, $op_no_quote) && is_string($val)) {
            $val = Database::get()->quote($val);
        }

        if ($operator == 'IN' || $operator == 'NOT IN') {
            $val = $this->in($val);
        }

        if ($operator == 'LIKE') {
            $val = "'$val'";
        }

        $this->type = $type;
        $this->col = $col;
        $this->op = $operator;
        $this->val = $val;
    }

    private function in($values) {
        if ($values === null) {
            return '()';
        }

        if (!is_array($values)) {
            return "($values)";
        }

        return '('.implode(',', $values).')';
    }

    public function get() {
        $query = $this->start;
        if ($this->type != 'start') {
            $query = strtoupper($this->type);
        }

        $query .= ' '.$this->col.' '.$this->op.' '.$this->val;

        return $query;
    }

}
