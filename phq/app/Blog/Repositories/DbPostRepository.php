<?php

namespace App\Blog\Repositories;

use App\Blog\Entities\Post;
use Exception;
use PHQ\Database\QueryBuilder\QueryResult;
use PHQ\Database\Repositories\DbRepository;

/**
 * Interface IPostRepository
 * @package App\Blog\Repositories
 */
class DbPostRepository extends DbRepository implements IPostRepository
{
    /**
     * @var string
     */
    protected $entity = Post::class;

    /**
     * @param int $nb
     * @return QueryResult|null
     * @throws Exception
     */
    public function getLastPost(int $nb = 10): ?QueryResult
    {
        return $this->makeQuery()
            ->select()
            ->from('posts')
            ->order('created_at', 'DESC')
            ->limit(0, $nb)->all();
    }
}
