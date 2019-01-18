<?php

namespace PHQ\Database;

class Hydrator
{
    /**
     * @param array $fields
     * @param $object
     * @return mixed
     * @throws \Exception
     */
    public static function hydrate(array $fields, $object)
    {
        if (is_string($object)) {
            $object = new $object();
        }
        $instance = $object;

        foreach ($fields as $key => $value) {
            self::callSetter($key, $instance, $value);
        }
        return $instance;
    }

    /**
     * @param string $fieldname
     * @return string
     */
    private static function getSetter(string $fieldname): string
    {
        return 'set'.self::getProperty($fieldname);
    }

    /**
     * @param string $fieldname
     * @return string
     */
    private static function getProperty(string $fieldname): string
    {
        return implode(
            '',
            array_map(
                'ucfirst',
                explode(
                    '_',
                    $fieldname
                )
            )
        );
    }

    /**
     * @param string $key
     * @param object $instance
     * @param mixed $value
     * @throws \Exception
     */
    private static function callSetter(string $key, $instance, $value): void
    {
        $method = self::getSetter($key);
        if (method_exists($instance, $method)) {
            $instance->$method($value);
        } else {
            $property = lcfirst(self::getProperty($key));
            if ($instance instanceof Entity) {
                $instance->autoSet($property, $value);
            } else {
                $instance->$property = $value;
            }
        }
    }
}
