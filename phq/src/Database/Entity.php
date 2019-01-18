<?php

namespace PHQ\Database;

use DateTime;
use JsonSerializable;
use PHQ\Utils\AutoGetterSetter;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class Entity implements JsonSerializable
{
    use AutoGetterSetter {
        __set as public autoSet;
    }

    /**
     * Default use uuid
     */
    const UUID = true;

    /**
     * Default created field name
     */
    const CREATED_AT = 'created_at';

    /**
     * Default updated field name
     */
    const UPDATED_AT = 'updated_at';

    /**
     * @var array
     */
    protected $guard = [];

    /**
     * @var array
     */
    protected $keep = [];

    /**
     * @var string $id
     */
    private $id;

    /**
     * @var string[] $updateFields
     */
    private $updateFields = [];

    /**
     * Entity constructor.
     * @param array $data
     * @throws \Exception
     */
    public function __construct(array $data = [])
    {
        Hydrator::hydrate($data, $this);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        $this->updateFields[$name] = $value;
        $this->autoSet($name, $value);
    }

    /**
     * @param $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id ?? '';
    }

    /**
     * @return string[]
     */
    public function getUpdateFields(): array
    {
        return $this->updateFields;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @throws ReflectionException
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $properties = $this->getProperties();
        $results = [];

        $class = new ReflectionClass(get_called_class());

        foreach ($properties as $property) {
            if ($class->hasProperty($property)) {
                $prop = $class->getProperty($property);
                $accessible = $this->isAccessible($prop);

                $prop->setAccessible(true);
                $results[$prop->getName()] = $prop->getValue($this);

                $prop->setAccessible($accessible);
            } elseif ($property === 'id') {
                $results['id'] = $this->id;
            }
        }

        return $results;
    }

    /**
     * @param $className
     * @return array
     * @throws ReflectionException
     */
    public function getAllPropertiesFor($className)
    {
        $class = new ReflectionClass($className);
        return array_map(function (ReflectionProperty $property) {
            return $property->getName();
        }, $class->getProperties());
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function getProperties()
    {
        $allProperties = $this->getAllPropertiesFor(get_called_class());
        $thisProperties = $this->getAllPropertiesFor(__CLASS__);

        // ID properties
        unset($thisProperties[array_search('id', $thisProperties)]);
        array_unshift($allProperties, 'id');

        $properties = $allProperties;
        if (!empty($this->keep)) {
            $properties = array_intersect($properties, $this->keep);
        }

        return array_diff($properties, $thisProperties, $this->guard);
    }

    /**
     * @param $value
     * @return DateTime
     */
    protected function setDateTime($value)
    {
        if ($value instanceof DateTime) {
            return $value;
        }
        return new DateTime($value);
    }

    private function isAccessible(ReflectionProperty $property)
    {
        if ($property->isPublic()) {
            return true;
        }

        return false;
    }
}
