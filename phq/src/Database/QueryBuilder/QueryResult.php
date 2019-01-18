<?php

namespace PHQ\Database\QueryBuilder;

use ArrayAccess;
use Countable;
use Iterator;
use JsonSerializable;
use PHQ\Database\Hydrator;

/**
 * Class QueryType
 * @package PHQ\Database\QueryBuilder
 */
class QueryResult implements ArrayAccess, Iterator, JsonSerializable, Countable
{
    /**
     * @var array $records
     */
    private $records;

    /**
     * @var array $hydratedRecords
     */
    private $hydratedRecords;

    /**
     * @var string $entity
     */
    private $entity;

    /**
     * @var int $index
     */
    private $index = 0;

    /**
     * QueryResult constructor.
     * @param array $records
     * @param null|string $entity
     */
    public function __construct(array $records, ?string $entity = null)
    {
        $this->records = $records;
        $this->entity = $entity;
    }

    /**
     * @param int $index
     * @return mixed
     * @throws \Exception
     */
    public function get(int $index)
    {
        if ($this->records[$index] === false) {
            return null;
        }

        if ($this->entity) {
            if (!isset($this->hydratedRecords[$index])) {
                $this->hydratedRecords[$index] = Hydrator::hydrate($this->records[$index], $this->entity);
            }
            return $this->hydratedRecords[$index];
        }
        return $this->entity;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->records);
    }

    public function __toString()
    {
        return json_encode($this);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->records[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     * @throws \Exception
     */
    public function offsetSet($offset, $value)
    {
        throw new \Exception('On ne peut  altérer des enregistrement');
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     * @throws \Exception
     */
    public function offsetUnset($offset)
    {
        throw new \Exception('On ne peut  altérer des enregistrement');
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->get($this->index);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next(): void
    {
        $this->index += 1;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid(): bool
    {
        return isset($this->records[$this->index]);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $result = [];

        foreach ($this as $name => $value) {
            $result[$name] = $value;
        }

        return $result;
    }
}
