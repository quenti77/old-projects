<?php

namespace Tests;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class Lib
{
    /**
     * Permet de set une methode en "accessible" pour la tester
     *
     * @param string $className
     * @param string $methodName
     * @return ReflectionMethod
     */
    public static function getMethod(string $className, string $methodName): ReflectionMethod
    {
        try {
            $class = new ReflectionClass($className);
            $method = $class->getMethod($methodName);
            $method->setAccessible(true);

            return $method;
        } catch (ReflectionException $e) {
            return null;
        }
    }
}
