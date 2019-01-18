<?php

namespace EasyHQ;

class Helper {

    public static function get($name) {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }

        return null;
    }

    public static function post($name) {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }

        return null;
    }

    public static function createCSRF() {
        $csrf = sha1(Config::randomString(5).time());

        Session::set('csrf', $csrf);
        return '<input type="hidden" name="_csrf" value="'.$csrf.'" />';
    }

}
