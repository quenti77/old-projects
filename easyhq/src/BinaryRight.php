<?php

namespace EasyHQ;

use App\Models\Groups;

class BinaryRight {

    private $actual;

    public function __construct($init = 0b00000000) {
        $this->actual = $init;
    }

    public function set($new_r) {
        $this->actual = $new_r;
    }

    public function get() {
        return $this->actual;
    }

    public function show() {
        return "0x".dechex($this->actual);
    }

    public function add($list) {
        $this->actual |= $list;
    }

    public function remove($list) {
        $tmp = ~$list;
        $this->actual &= $tmp;
    }

    public function compare($list) {
        return !(( (int)$this->actual & $list) ^ ($list));
    }

}
