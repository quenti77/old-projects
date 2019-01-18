<?php

namespace PHQ\Database\Repositories;

use Exception;
use PHQ\Database\Entity;
use PHQ\Database\QueryBuilder\QueryResult;

interface IDbRepository
{
    /**
     * @param string[] $fields
     * @return QueryResult
     * @throws Exception
     */
    public function findAll(array $fields = []): QueryResult;

    /**
     * @param $id
     * @param array $fields
     * @return Entity|null
     */
    public function findById($id, array $fields = []): ?Entity;

    /**
     * @param Entity $entity
     * @return Entity
     */
    public function save(Entity $entity): Entity;
}
