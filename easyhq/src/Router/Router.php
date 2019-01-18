<?php

namespace EasyHQ\Router;

use EasyHQ\Config;
use EasyHQ\Exception\RouterException;

class Router {

    private static $routes = [];
    private static $namedRoutes = [];

    public static function get($path, $callable, $name = null) {
        return self::add('GET', $path, $callable, $name);
    }

    public static function post($path, $callable, $name = null) {
        return self::add('POST', $path, $callable, $name);
    }

    public static function controller($pathStart, $controller) {
        if ($controller === null) {
            return false;
        }

        $class = "App\\Controllers\\".$controller."Controller";
        $extends = 'EasyHQ\\Base\\BaseController';
        if (!class_exists($class)) {
            throw new RouterException('Class does not exist : '.$class);
        }

        $class = new \ReflectionClass($class);
        foreach ($class->getMethods() as $method) {
            // All function in class
            if ($method->getDeclaringClass()->getName() !== $extends && $method->isPublic()) {
                $methodFinal = 'GET';
                if (strpos($method->getName(), 'post') === 0) {
                    $methodFinal = 'POST';
                }

                $callable = $controller.'@'.$method->getName();

                $methodName = preg_replace('#get|post#', '', $method->getName());
                $methodName = strtolower(substr($methodName, 0, 1)).substr($methodName, 1);

                $path = $pathStart;
                if (!preg_match('#/$#', $pathStart)) {
                    $path .= '/';
                }

                $parameters = [];
                foreach($method->getParameters() as $param) {
                    if (!$param->isOptional()) {
                        $parameters[] = ':'.strtolower($param->getName());
                    }
                }

                $path .= strtolower($controller).'/'.$methodName;
                if (!empty($parameters)) {
                    $path .= '/'.implode('-', $parameters);
                }

                self::add($methodFinal, $path, $callable, null);
            }
        }
    }

    private static function add($method, $path, $callable, $name) {
        $route = new Route($path, $callable);
        self::$routes[$method][] = $route;

        if (is_string($callable) && $name === null) {
            $name = str_replace('@', '.', strtolower($callable));
            $name = str_replace('\\', ':', strtolower($name));
        }

        if ($name) {
            self::$namedRoutes[$name] = $route;
        }

        return $route;
    }

    public static function init() {
        $url = (isset($_GET['url']) ? $_GET['url'] : '/');

        $path = __DIR__.'/../../app/routes.php';
        require "$path";
        try {
            Router::parse($url);
        } catch (RouterException $e) {
            if (isset($_GET['url']) && $_GET['url'] == 'error/404') {
                throw new \Exception('Too many Redirects');
            }

            if (Config::getField('DEV_ENV')) {
                throw new RouterException($e->getMessage(), 0, $e);
            } else {
                if (headers_sent()) {
                    throw new \Exception('Headers already sent !');
                }

                header('location: /error/404');
                exit();
            }
        }
    }

    private static function parse($url) {
        $method = $_SERVER['REQUEST_METHOD'];
        if ( !isset(self::$routes[$method]) ) {
            throw new RouterException('No routes with this request method');
        }

        foreach(self::$routes[$method] as $route) {
            if ($route->match($url)) {
                return $route->call();
            }
        }

        throw new RouterException('No routes matches with : '.$url);
    }

    public static function url($name, $params = []) {
        if (!isset(self::$namedRoutes[$name])) {
            throw new RouterException('No route matches this name');
        }

        return '/'.self::$namedRoutes[$name]->getUrl($params);
    }

    public static function redirect($name, $params = []) {
        if (!headers_sent()) {
            header('location: '.Router::url($name, $params));
            exit();
        }
    }
}


