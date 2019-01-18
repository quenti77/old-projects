<?php

namespace EasyHQ\Router;

use EasyHQ\Exception\RouterException;

class Route {

    private $path;
    private $callable;
    private $matches = [];
    private $params = [];

    public function __construct($path, $callable) {
        $this->path = trim($path, '/');
        $this->callable = $callable;
    }

    public function match($url) {
        $url = trim($url, '/');
        $path = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->path);
        $regex = "#^$path$#i";

        if (!preg_match($regex, $url, $matches)) {
            return false;
        }

        array_shift($matches);
        $this->matches = $matches;
        return true;
    }

    public function with($param, $regex) {
        $this->params[$param] = str_replace('(', '(?:', $regex);
        return $this;
    }

    public function call() {
        if (is_string($this->callable)) {
            $pathParam = explode('@', $this->callable);
            $controller = "App\\Controllers\\".$pathParam[0]."Controller";

            if (!class_exists($controller)) {
                throw new RouterException('Class does not exist : '.$controller);
            }

            $controllerObj = new $controller();

            if ( !in_array($pathParam[1], get_class_methods($controllerObj)) ) {
                throw new RouterException('Method "'.$pathParam[1].'" does not exist in '.$controller);
            }

            return call_user_func_array([$controllerObj, $pathParam[1]], $this->matches);
        } else {
            return call_user_func_array($this->callable, $this->matches);
        }
    }

    private function paramMatch($match) {
        if ( isset($this->params[ $match[1] ]) ) {
            return '('.$this->params[$match[1]].')';
        }

        return '([^/]+)';
    }

    public function getUrl($params) {
        $path = $this->path;

        foreach($params as $k => $v) {
            $path = str_replace(":$k", $v, $path);
        }

        return $path;
    }

}
