<?php

namespace EasyHQ;

class Database {

    private $db = null;
    private $connected = false;
    private static $_instance = null;

    private function __construct() {
        try {
            $bdd_info = Config::getField('BDD');
            $bdd_type = $bdd_info['type'];
            $bdd_host = $bdd_info['host'];

            $bdd_name = $bdd_info['name'];
            $bdd_user = $bdd_info['user'];
            $bdd_pass = $bdd_info['pass'];

            $this->db = new \PDO($bdd_type.':host='.$bdd_host.';dbname='.$bdd_name,
                $bdd_user, $bdd_pass);

            $this->db->exec("SET CHARACTER SET utf8");
            $this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
            if (Config::getField('DEV_ENV')) {
                $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            }

            $this->connected = true;
        } catch (\Exception $e) {
            throw new \Exception("Probleme LOOOOL");
        }
    }

    /* Static function */
    public static function get() {
        if ( is_null(self::$_instance) ) {
            self::$_instance = new Database();
        }

        return self::$_instance->db;
    }

    public static function isConnected() {
        if ( is_null(self::$_instance) ) {
            return false;
        }

        return self::$_instance->connected;
    }

}
