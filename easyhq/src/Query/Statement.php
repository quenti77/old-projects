<?php

namespace EasyHQ\Query;

use EasyHQ\Config;
use EasyHQ\Database;

class Statement {

    const TYPE_SELECT = 1;
    const TYPE_INSERT = 2;
    const TYPE_UPDATE = 3;
    const TYPE_DELETE = 4;

    private $class_name;
    private $short_class_name;

    private $select = [];
    private $table = [];
    private $join = [];

    private $where = null;
    private $groupWhere = [];

    private $order = [];
    private $group = [];

    private $set = [];
    private $cols = '';
    private $values = [];

    private $type = 0;

    public function __construct($table, $class_name, $short_class_name) {
        $table = explode('@', $table);

        if ($table[0] === '') {
            $table[0] = strtolower($short_class_name);
        }

        if (!isset($table[1])) {
            $table[1] = $table[0];
        }

        $this->addFrom($table[0], $table[1]);
        $this->class_name = $class_name;
        $this->short_class_name = $short_class_name;
    }

    /* Return always array */
    private function checkTab($check = null) {
        if ($check === null) {
            $check = [];
        }

        if (!is_array($check)) {
            $check = [$check];
        }

        return $check;
    }

    private function check($status, $message = 'Bad statement') {
        if ($this->type != $status) {
            throw new \InvalidArgumentException($message);
        }
    }

    /* Start query */
    public function select() {
        return $this->start(Statement::TYPE_SELECT);
    }

    public function insert() {
        return $this->start(Statement::TYPE_INSERT);
    }

    public function update() {
        return $this->start(Statement::TYPE_UPDATE);
    }

    public function delete() {
        return $this->start(Statement::TYPE_DELETE);
    }

    private function start($type) {
        $this->check(0, 'Statement already started');

        $this->type = $type;
        return $this;
    }

    /* Target field */
    public function addFields($cols) {
        $this->check(Statement::TYPE_SELECT);
        $cols = $this->checkTab($cols);

        $field = [];
        foreach($cols as $key => $value) {
            if (is_string($key)) {
                $field[] = "$key AS $value";
            } else {
                $field[] = "$value";
            }
        }

        $this->select = array_merge($this->select, $field);
        return $this;
    }

    /* From clause */
    public function addFrom($from, $alias = '') {
        if ($alias === '') {
            if (is_string($from)) {
                $alias = $from;
            } else {
                $alias = Config::randomString(3);
            }
        }

        $this->table = array_merge($this->table, [$alias => ['type' => 'from', 'table' => $from]]);

        return $this;
    }

    /* Join clause */
    public function innerJoin($table, $alias) {
        return $this->join('inner', $table, $alias);
    }

    public function leftJoin($table, $alias) {
        return $this->join('left', $table, $alias);
    }

    public function rightJoin($table, $alias) {
        return $this->join('right', $table, $alias);
    }

    private function join($type, $table, $alias) {
        $this->check(Statement::TYPE_SELECT);
        $this->join[] = new JoinStatement($type, $table, $alias);
        $this->table = array_merge($this->table, [$alias => ['type' => 'join', 'table' => $table]]);
        return $this;
    }

    public function onJoin($col, $op, $val = null, $escape = false) {
        return $this->addConditionJoin('start', $col, $op, $val, $escape);
    }

    public function andJoin($col, $op, $val = null, $escape = false) {
        return $this->addConditionJoin('and', $col, $op, $val, $escape);
    }

    public function orJoin($col, $op, $val = null, $escape = false) {
        return $this->addConditionJoin('or', $col, $op, $val, $escape);
    }

    private function addConditionJoin($type, $col, $op, $val, $escape) {
        $this->check(Statement::TYPE_SELECT);
        $last = end($this->join);

        if ($last) {
            $last->addCondition($type, $col, $op, $val, $escape);
        }

        reset($this->join);
        return $this;
    }

    /* Where clause */
    public function where($col, $operator, $val = null, $escape = true) {
        if ($this->where == null) {
            $this->where = new WhereStatement();
        }

        $this->where->addWhere('start', $col, $operator, $val, $escape);
        return $this;
    }

    public function andWhere($col, $operator, $val = null, $escape = true) {
        if ($this->where == null) {
            return $this->where($col, $operator, $val, $escape);
        }

        $this->where->addWhere('and', $col, $operator, $val, $escape);
        return $this;
    }

    public function orWhere($col, $operator, $val = null, $escape = true) {
        if (empty($this->where)) {
            return $this->where($col, $operator, $val, $escape);
        }

        $this->where->addWhere('or', $col, $operator, $val, $escape);
        return $this;
    }

    public function andGroup($listCond) {
        $this->addGroup('AND', $listCond);
        return $this;
    }

    public function orGroup($listCond) {
        $this->addGroup('OR', $listCond);
        return $this;
    }

    private function addGroup($type, $listCond) {
        $this->groupWhere[] = [$type, $listCond];
    }

    /* Order by */
    public function orderBy($col, $direction = 'ASC') {
        $this->check(Statement::TYPE_SELECT);
        $this->order[] = "$col $direction";

        return $this;
    }

    public function groupBy($col) {
        $this->check(Statement::TYPE_SELECT);
        $this->group[] = "$col";

        return $this;
    }

    /* Processing */
    public function sql() {
        if ($this->type != Statement::TYPE_SELECT) {
            throw new \InvalidArgumentException("Query not start");
        }

        // Start
        $query = 'SELECT ';

        // Fields
        if (empty($this->select)) {
            foreach($this->table as $alias => $table) {
                $this->addFields("$alias.*");
            }
        }
        $query .= implode(', ', $this->select);

        // From
        $query .= ' FROM ';
        $froms = [];
        foreach($this->table as $alias => $table) {
            if ($table['type'] === 'from') {
                if (is_string($table['table'])) {
                    $froms[] = ''.$table['table'].' AS '.$alias;
                } else {
                    $froms[] = '('.$table['table']->sql().') AS '.$alias;
                }
            }
        }
        $query .= implode(', ', $froms).' ';

        // Join
        if (!empty($this->join)) {
            foreach($this->join as $j) {
                $query .= $j->get();
            }
        }

        // Where
        $query .= $this->whereSQL();

        // Order by
        if (!empty($this->order)) {
            $query .= ' ORDER BY '.implode(', ', $this->order);
        }

        // Group by
        if (!empty($this->group)) {
            $query .= ' GROUP BY '.implode(', ', $this->group);
        }

        return $query;
    }

    public function get($offset = 0, $limit = 0) {
        $q = $this->sql();

        if ($offset < 0) { $offset = 0; }
        if ($limit < 0) { $limit = 0; }

        if ($limit == 0) {
            if ($offset != 0) {
                $q .= ' LIMIT :offset';
            }
        } else {
            $q .= ' LIMIT :offset, :limit';
        }

        //var_dump($q);
        $req = Database::get()->prepare($q);

        if ($limit == 0) {
            if ($offset != 0) {
                $req->bindValue('offset', $offset, \PDO::PARAM_INT);
            }
        } else {
            $req->bindValue('offset', $offset, \PDO::PARAM_INT);
            $req->bindValue('limit', $limit, \PDO::PARAM_INT);
        }

        $req->execute();

        $data = $req->fetchAll(\PDO::FETCH_CLASS, $this->class_name);
        foreach ($data as $d) {
            foreach($d->desc as $k => $v) {
                if (isset($d->$k)) {
                    $d->whereArray[$k] = $d->$k;
                }
            }
        }
        return $data;
    }

    public function set($col, $val, $escape = true) {
        $this->check(Statement::TYPE_UPDATE);
        if ($escape) {
            $val = Database::get()->quote($val);
        }

        $this->set[] = "$col = $val";
        return $this;
    }

    public function columns($cols) {
        $this->check(Statement::TYPE_INSERT);
        $cols = implode(', ', $this->checkTab($cols));
        $this->cols = "($cols)";
        return $this;
    }

    public function values($vals) {
        $this->check(Statement::TYPE_INSERT);
        $vals = implode(', ', $this->checkTab($vals));

        $this->values[] = "($vals)";
        return $this;
    }

    /*
     * Finalise les requête de type INSERT, UPDATE et DELETE
     */
    public function save() {
        $first = reset($this->table);
        $query = $first['table'].' ';

        switch($this->type) {
            case Statement::TYPE_INSERT:
                $query = 'INSERT INTO '.$query;
                $query .= $this->insertSQL();
                break;
            case Statement::TYPE_UPDATE:
                $query = 'UPDATE '.$query;
                $query .= $this->updateSQL();
                break;
            case Statement::TYPE_DELETE:
                $query = 'DELETE FROM '.$query;
                $query .= $this->deleteSQL();
                break;
        }

        $req = Database::get()->prepare($query);
        return $req->execute();
    }

    private function insertSQL() {
        $query = $this->cols.' VALUES ';
        $query .= implode(', ', $this->values);

        return $query;
    }

    private function updateSQL() {
        $query = 'SET '.implode(', ', $this->set);
        $query .= $this->whereSQL();
        return $query;
    }

    private function deleteSQL() {
        return $this->whereSQL();
    }

    private function whereSQL() {
        $query = '';

        if ($this->where != null) {
            $query = $this->where->get();
        }

        if (!empty($this->groupWhere)) {
            foreach($this->groupWhere as $gw) {
                $query .= ' '.$gw[0].' (';

                foreach($gw[1] as $cond) {
                    $query .= ' '.$cond->get();
                }
                $query .= ') ';
            }
        }

        return $query;
    }

}
