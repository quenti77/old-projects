<?php

namespace EasyHQ;

class Config {

    private $config = null;
    private static $_instance = null;

    CONST ENVIRONMENT = 'dev';

    private function __construct($mode) {
        $path =  __DIR__."/../app/Config/$mode.php";

        if (!file_exists($path)) {
            throw new \Exception('Config file not found');
        }

        require "$path";
        if (!isset($config)) {
            throw new \Exception('No config defined in file');
        }

        $this->config = $config;
    }

    /* Static function */
    public static function setup() {
        if ( is_null(self::$_instance) ) {
            self::$_instance = new Config(Config::ENVIRONMENT);
        }

        return self::$_instance;
    }

    public static function getField($name) {
        $config = Config::setup();

        if ( !is_null($config) && is_array($config->config)) {
            if (isset($config->config[$name])) {
                return $config->config[$name];
            }
        }

        return null;
    }

    public static function getClassName($class_name) {
        $reflect = new \ReflectionClass($class_name);
        return $reflect->getShortName();
    }

    public static function randomString($length) {
        return (substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ', $length)), 0, $length));
    }

}
