<?php

namespace PHQ\Utils;

use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

trait AutoGetterSetter
{
    use Inflectorable;

    /**
     * Permet de récupéré la valeur
     *
     * @param string $name
     * @return mixed
     * @throws ReflectionException
     * @throws Exception
     */
    public function __get($name)
    {
        $methodName = 'get'.ucfirst($this->getCamelCase($name));
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }

        $property = $this->getProperty($name, get_called_class());
        if ($property) {
            $property->setAccessible(true);
            $value = $property->getValue($this);
            $property->setAccessible(false);
            return $value;
        }
        throw new Exception("Unknown value for {$name}");
    }

    /**
     * Permet de définir la valeur
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @throws Exception
     */
    public function __set($name, $value)
    {
        $methodName = 'set'.ucfirst($this->getCamelCase($name));
        if (method_exists($this, $methodName)) {
            return $this->$methodName($value);
        }

        $property = $this->getProperty($name, get_called_class());
        if ($property) {
            $property->setAccessible(true);
            $property->setValue($this, $value);
            $property->setAccessible(false);
            return true;
        }
        throw new Exception("Unknown value for {$name}");
    }

    /**
     * @param $name
     * @return bool
     * @throws ReflectionException
     */
    public function __isset($name): bool
    {
        $property = $this->getProperty($name, get_called_class());
        return $property !== null;
    }

    /**
     * Permet de récupéré une instance de la propriété
     *
     * @param string $name Le nom de la propriété
     * @param string $className
     * @return null|ReflectionProperty
     * @throws ReflectionException
     */
    public function getProperty($name, $className)
    {
        $nameUnderscore = '_'.$name;
        $reflectionClass = new ReflectionClass($className);
        foreach ($reflectionClass->getProperties() as $property) {
            if ($property->getName() === $name || $property->getName() === $nameUnderscore) {
                return $property;
            }
        }
        $reflectionClass = $reflectionClass->getParentClass();
        if ($reflectionClass) {
            return $this->getProperty($name, $reflectionClass->getName());
        }
        return null;
    }
}
