<?php

namespace App\Blog\Repositories;

use PHQ\Database\Repositories\IDbRepository;
use PHQ\Database\QueryBuilder\QueryResult;

/**
 * Interface IPostRepository
 * @package App\Blog\Repositories
 */
interface IPostRepository extends IDbRepository
{
    /**
     * @param int $nb
     * @return QueryResult|null
     */
    public function getLastPost(int $nb = 10): ?QueryResult;
}
