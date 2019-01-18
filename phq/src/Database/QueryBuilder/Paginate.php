<?php

namespace PHQ\Database\QueryBuilder;

use Exception;

/**
 * Class Paginate
 *
 * @package PHQ\Database\QueryBuilder
 */
class Paginate
{
    /**
     * @var Query $query
     */
    private $query;

    /**
     * Paginate constructor.
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * @param int $page
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function get(int $page, int $limit = 10): array
    {
        if ($limit < 1) {
            throw new Exception('Limit value not possible to null or negative');
        }

        $countQuery = clone ($this->query);
        $count = $countQuery->count();
        $nbMaxPage = ceil($count / $limit);

        if ($page < 1) {
            $page = 1;
        }

        if ($page > $nbMaxPage) {
            $page = $nbMaxPage;
        }

        $previousPage = 1;
        if (($page - 1) > 1) {
            $previousPage = ($page - 1);
        }

        $nextPage = $nbMaxPage;
        if (($page + 1) < $nbMaxPage) {
            $nextPage = ($page + 1);
        }

        $offset = ($page - 1) * $limit;

        return [
            'previous' => $previousPage,
            'current' => $page,
            'next' => $nextPage,
            'max' => $nbMaxPage,
            'items' => $this->query->limit($offset, $limit)->all()
        ];
    }
}
