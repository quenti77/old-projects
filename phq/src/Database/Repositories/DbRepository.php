<?php

namespace PHQ\Database\Repositories;

use DateTime;
use Exception;
use PHQ\Database\Entity;
use PHQ\Database\IConnection;
use PHQ\Database\IStatement;
use PHQ\Database\QueryBuilder\Query;
use PHQ\Database\QueryBuilder\QueryResult;
use PHQ\Utils\Inflector;
use PHQ\Utils\Inflectorable;
use ReflectionException;

class DbRepository extends Repository implements IDbRepository
{
    use Inflectorable;

    /**
     * @var IConnection $connection
     */
    protected $connection;

    /**
     * Nom de la table si différent du nom de l'entité
     * @var string $table
     */
    protected $table;

    /**
     * @var string $primaryKey
     */
    protected $primaryKey = 'id';

    /**
     * Nom de la classe représentant un enregistrement
     * @var string $entity
     */
    protected $entity;

    public function __construct(IConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string[] $fields
     * @return QueryResult
     * @throws Exception
     */
    public function findAll(array $fields = []): QueryResult
    {
        $table = $this->getTableName();

        return $this->makeQuery()
            ->select($fields)
            ->from($table)
            ->all();
    }

    /**
     * @param $id
     * @param string[] $fields
     * @return Entity|null
     * @throws Exception
     */
    public function findById($id, array $fields = []): ?Entity
    {
        $table = $this->getTableName();

        return $this->makeQuery()
            ->select($fields)
            ->from($table)
            ->where($this->primaryKey, '=', $id)
            ->first();
    }

    /**
     * @param Entity $entity
     * @return Entity
     * @throws ReflectionException
     * @throws Exception
     */
    public function save(Entity $entity): Entity
    {
        if (empty($entity->getId())) {
            $this->insert($entity);
        } else {
            $this->update($entity);
        }

        return $entity;
    }

    /**
     * @param Query $query
     * @return Query
     */
    protected function addEntity(Query $query): Query
    {
        if ($this->entity) {
            return $query->into($this->entity);
        }

        return $query;
    }

    /**
     * @return Query
     */
    protected function makeQuery(): Query
    {
        return $this->addEntity(new Query($this->connection));
    }

    /**
     * @return string
     */
    protected function getTableName(): string
    {
        if (!empty($this->table)) {
            return $this->table;
        }

        $repositoryPath = explode('\\', get_called_class());
        $repositoryName = $repositoryPath[count($repositoryPath) - 1];

        if (substr($repositoryName, 0, 2) === 'Db') {
            $repositoryName = substr($repositoryName, 2);
        }

        if (substr($repositoryName, -10) === 'Repository') {
            $repositoryName = substr($repositoryName, 0, -10);
        }

        $this->table = Inflector::pluralize($this->getUnderscoreCase(lcfirst($repositoryName)));
        return $this->table;
    }

    /**
     * @param Entity $entity
     * @return IStatement
     * @throws ReflectionException
     * @throws Exception
     */
    private function insert(Entity $entity): IStatement
    {
        $table = $this->getTableName();
        $query = $this->makeQuery()->insert($table);

        $properties = $entity->getProperties();

        foreach ($properties as $property) {
            $fieldName = $this->getUnderscoreCase($property);
            $value = $entity->$property;

            if (empty($value) && !empty($entity::CREATED_AT) && $entity::CREATED_AT === $fieldName) {
                $entity->$property = $value = new DateTime();
            }

            $query->value($fieldName, $value);
        }

        return $query->execute();
    }

    /**
     * @param Entity $entity
     * @return IStatement|null
     * @throws Exception
     */
    private function update(Entity $entity): ?IStatement
    {
        $table = $this->getTableName();
        $query = $this->makeQuery()->update($table);

        $properties = array_keys($entity->getUpdateFields());
        unset($properties[$this->primaryKey]);

        if (empty($properties)) {
            return null;
        }

        $updatedFieldUpdate = false;
        foreach ($properties as $property) {
            $fieldName = $this->getUnderscoreCase($property);
            $value = $entity->$property;

            if (!empty($entity::UPDATED_AT) && $entity::UPDATED_AT === $fieldName) {
                $updatedFieldUpdate = true;
            }

            $query->value($fieldName, $value);
        }

        if (!$updatedFieldUpdate) {
            $query->value($entity::UPDATED_AT, new DateTime());
        }

        $primaryKey = $this->primaryKey;
        $query->where($this->primaryKey, '=', $entity->$primaryKey);

        return $query->execute();
    }
}
