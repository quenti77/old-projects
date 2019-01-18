<?php

namespace PHQ\Database\QueryBuilder;

use Exception;
use PHQ\Database\Entity;
use PHQ\Database\IConnection;
use PHQ\Database\IStatement;

/**
 * Class Query
 * @package PHQ\Database\QueryBuilder
 */
class Query
{
    /**
     * @var string
     */
    private $type = QueryType::NONE;

    /**
     * @var IConnection $connection
     */
    private $connection;

    /**
     * @var bool $masterQuery
     */
    private $masterQuery;

    /**
     * @var array $from
     */
    private $from = [];

    /**
     * @var array $fields
     */
    private $fields = [];

    /**
     * Tous les paramètres variable de la requête.
     * INSERT INTO posts VALUES (:a, :b, :c)
     * [':a' => $a, ':b' => $b, ':c' => $c]
     * @var array $params
     */
    private $params = [];

    /**
     * @var array $values
     */
    private $values = [];

    /**
     * @var array $join
     */
    private $join = [];

    /**
     * @var array $where
     */
    private $where = [];

    /**
     * @var string[] $group
     */
    private $groups = [];

    /**
     * @var array $group
     */
    private $orders = [];

    /**
     * @var array $limit
     */
    private $limit;

    /**
     * @var string|null $entity
     */
    private $entity = null;

    /**
     * Init une nouvelle query
     *
     * @param IConnection $connection
     * @return Query
     * @throws \Exception
     */
    public static function makeQuery(IConnection $connection): Query
    {
        $query = new Query($connection);
        return $query->select();
    }

    /**
     * Query constructor.
     * @param IConnection $connection
     */
    public function __construct(IConnection $connection)
    {
        $this->connection = $connection;
        $this->masterQuery = true;
    }

    /**
     * @return IConnection
     */
    public function getConnection(): IConnection
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isMasterQuery(): bool
    {
        return $this->masterQuery;
    }

    /**
     * @param string $entity
     * @return Query
     */
    public function into(string $entity): self
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @param mixed ...$fieldSelect
     * @return Query
     * @throws \Exception
     */
    public function select(...$fieldSelect): self
    {
        $this->setType(QueryType::SELECT);

        foreach ($fieldSelect as $value) {
            if (is_string($value)) {
                $this->columns($value);
            } elseif (is_array($value)) {
                foreach ($value as $column => $alias) {
                    if (is_int($column)) {
                        $this->columns($alias);
                    } else {
                        $this->columns($column, $alias);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param $column
     * @param null|string $alias
     * @return Query
     * @throws \Exception
     */
    public function columns(string $column, ?string $alias = null): self
    {
        if ($this->type !== QueryType::SELECT) {
            throw new \Exception('Le FROM ne peut pas être multiple pour les requêtes INSERT ou DELETE');
        }

        if ($alias) {
            $this->fields[$alias] = $column;
        } else {
            $this->fields[] = $column;
        }
        return $this;
    }

    /**
     * @param string $from
     * @param bool $uuid
     * @return Query
     * @throws \Exception
     */
    public function insert(string $from, bool $uuid = true): self
    {
        $this->setType(QueryType::INSERT);
        $this->setFrom($from);

        if ($uuid) {
            $this->value('id', 'UUID()', false);
        }

        return $this;
    }

    /**
     * @param string $from
     * @param null|string $alias
     * @return Query
     * @throws \Exception
     */
    public function update(string $from, ?string $alias = null): self
    {
        $this->setType(QueryType::UPDATE);
        $this->from($from, $alias);

        return $this;
    }

    /**
     * @param string $from
     * @return Query
     * @throws \Exception
     */
    public function delete(string $from): self
    {
        $this->setType(QueryType::DELETE);
        $this->setFrom($from);

        return $this;
    }

    /**
     * @param string|callable $from
     * @param null|string $alias
     * @return Query
     * @throws \Exception
     */
    public function from($from, ?string $alias = null): self
    {
        if (in_array($this->type, [QueryType::INSERT, QueryType::DELETE])) {
            throw new \Exception('Le FROM ne peut pas être multiple pour les requêtes INSERT ou DELETE');
        }

        if (is_callable($from)) {
            if ($this->type !== QueryType::SELECT) {
                throw new \Exception('Le FROM ne peut pas être une sous-requête pour un UPDATE');
            }

            $query = Query::makeQuery($this->connection);
            $query->turnOffMasterQuery();
            $from($query);

            $statement = (string)$query;
            $from = "(".$statement.")";
        }

        if ($alias) {
            $this->from[$alias] = $from;
        } else {
            $this->from[] = $from;
        }
        return $this;
    }

    /**
     * @param array $values
     * @return Query
     * @throws \Exception
     */
    public function values(array $values): self
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $this->value($key, $value[0], $value[1]);
            } else {
                $this->value($key, $value);
            }
        }
        return $this;
    }

    /**
     * Permet d'ajouter des valeurs pour l'insert
     *
     * @param string $column
     * @param $value
     * @param bool $escape
     * @return Query
     * @throws \Exception
     */
    public function value(string $column, $value, bool $escape = true): self
    {
        if (in_array($this->type, [QueryType::SELECT, QueryType::DELETE])) {
            throw new \Exception('On ne peut pas ajouter des valeurs à une requête SELECT ou DELETE');
        }

        if ($escape) {
            $param = ':'.$column;
            $this->values[$column] = $param;
            return $this->params([$param => $value]);
        }
        $this->values[$column] = $value;
        return $this;
    }

    /**
     * @param callable $on
     * @return Query
     * @throws \Exception
     */
    public function innerJoin(callable $on): self
    {
        return $this->addJoin('INNER JOIN', $on);
    }

    /**
     * @param callable $on
     * @return Query
     * @throws \Exception
     */
    public function leftJoin(callable $on): self
    {
        return $this->addJoin('LEFT JOIN', $on);
    }

    /**
     * @param callable $on
     * @return Query
     * @throws \Exception
     */
    public function rightJoin(callable $on): self
    {
        return $this->addJoin('RIGHT JOIN', $on);
    }

    /**
     * @param string $column
     * @param string $op
     * @param null $value
     * @param bool $escape
     * @return Query
     */
    public function where(string $column, string $op, $value = null, bool $escape = true): self
    {
        $prefix = 'WHERE';
        if (!empty($this->where)) {
            $prefix = 'AND';
        }
        return $this->addWhere($column, $op, $value, $prefix, $escape);
    }

    /**
     * @param string $column
     * @param string $op
     * @param null $value
     * @param bool $escape
     * @return Query
     */
    public function orWhere(string $column, string $op, $value = null, bool $escape = true): self
    {
        if (empty($this->where)) {
            return $this->where($column, $op, $value);
        }
        return $this->addWhere($column, $op, $value, 'OR', $escape);
    }

    /**
     * @param callable $callback
     * @return Query
     * @throws \Exception
     */
    public function whereGroup(callable $callback): self
    {
        $prefix = 'WHERE';
        if (!empty($this->where)) {
            $prefix = 'AND';
        }

        return $this->whereGroupAdd($prefix, $callback);
    }

    /**
     * @param callable $callback
     * @return Query
     * @throws \Exception
     */
    public function orWhereGroup(callable $callback): self
    {
        if (empty($this->where)) {
            return $this->whereGroup($callback);
        }
        return $this->whereGroupAdd('OR', $callback);
    }

    /**
     * @param string[] ...$groups
     * @return Query
     * @throws \Exception
     */
    public function groups(string ...$groups): self
    {
        if ($this->type !== QueryType::SELECT) {
            throw new \Exception('On ne peut pas ajouter des valeurs à une requête SELECT ou DELETE');
        }

        $this->groups = array_merge($this->groups, $groups);
        return $this;
    }

    /**
     * @param array $orders
     * @return Query
     * @throws \Exception
     */
    public function orders(array $orders): self
    {
        foreach ($orders as $key => $value) {
            if (is_int($key)) {
                $this->order($value);
            } else {
                $this->order($key, $value);
            }
        }
        return $this;
    }

    /**
     * Permet d'ajouter des valeurs pour l'insert
     *
     * @param string $column
     * @param string $type
     * @return Query
     * @throws \Exception
     */
    public function order(string $column, string $type = 'ASC'): self
    {
        if ($this->type !== QueryType::SELECT) {
            throw new \Exception('On ne peut pas ajouter des valeurs à une requête SELECT ou DELETE');
        }

        if (!in_array($type, ['ASC', 'DESC'])) {
            throw  new \Exception('On ne peut trier que par ordre croissant ou décroissant');
        }

        $this->orders[] = "{$column} {$type}";

        return $this;
    }

    /**
     * @param $offset
     * @param $limit
     * @return Query
     * @throws \Exception
     */
    public function limit($offset, $limit = null): self
    {
        if (in_array($this->type, [QueryType::INSERT, QueryType::UPDATE])) {
            throw new \Exception('On ne peut pas ajouter des valeurs à une requête INSERT ou UPDATE');
        }

        if (is_int($offset)) {
            $this->params([
                ':offsetSqlLimit' => $offset
            ]);
            $offset = ':offsetSqlLimit';
        }

        if (is_int($limit)) {
            $this->params([
                ':limitSqlLimit' => $limit
            ]);
            $limit = ':limitSqlLimit';
        }

        $this->limit = [$offset, $limit];
        return $this;
    }

    /**
     * @param array $params
     * @return Query
     */
    public function params(array $params): self
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function __toString()
    {
        $parts = [$this->type];
        switch ($this->type) {
            case QueryType::SELECT:
                $parts = $this->selectRender($parts);
                break;
            case QueryType::INSERT:
                $parts = $this->insertRender($parts);
                break;
            case QueryType::UPDATE:
                $parts = $this->updateRender($parts);
                break;
            case QueryType::DELETE:
                $parts = $this->deleteRender($parts);
                break;
            default:
                throw new \Exception('La requête n\'a pas de type (SELECT, INSERT, UPDATE, DELETE)');
        }

        return implode(' ', $parts);
    }

    /**
     * @return IStatement
     * @throws \Exception
     */
    public function execute(): IStatement
    {
        $statement = $this->__toString();
        return $this->connection->request($statement, $this->params);
    }

    /**
     * @return QueryResult
     * @throws \Exception
     */
    public function all(): QueryResult
    {
        return new QueryResult($this->execute()->fetchAll(), $this->entity);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function first(): ?Entity
    {
        return (new QueryResult([$this->execute()->fetch()], $this->entity))->get(0);
    }

    /**
     * @return int
     * @throws Exception
     */
    public function count(): int
    {
        $field = 'nb'.uniqid();
        $this->columns('COUNT(*)', $field);

        $result = $this->execute()->fetch();
        return $result[$field] ?? 0;
    }

    /**
     * @param array $parts
     * @return array
     * @throws \Exception
     */
    private function selectRender(array $parts): array
    {
        if (empty($this->fields)) {
            $parts[] = '*';
        } else {
            $parts[] = $this->generateTabWithAlias($this->fields);
        }

        if (empty($this->from)) {
            throw new \Exception('Aucune table sélectionné');
        }
        $parts[] = 'FROM';
        $parts[] = $this->generateTabWithAlias($this->from);

        if (!empty($this->join)) {
            $parts[] = $this->generateJoin($this->join);
        }

        if (!empty($this->where)) {
            $parts[] = $this->generateCondition($this->where);
        }

        if (!empty($this->groups)) {
            $parts[] = 'GROUP BY';
            $parts[] = implode(', ', $this->groups);
        }

        if (!empty($this->orders)) {
            $parts[] = 'ORDER BY';
            $parts[] = implode(', ', $this->orders);
        }

        if ($this->limit) {
            [$limit, $offset] = $this->limit;
            $str = "LIMIT {$offset}";
            if ($limit !== null) {
                $str .= ' OFFSET '.$limit;
            }
            $parts[] = $str;
        }

        return $parts;
    }

    /**
     * @param array $parts
     * @return array
     */
    private function insertRender(array $parts): array
    {
        $parts[] = $this->from[0];

        $columns = array_keys($this->values);
        $parts[] = '('.implode(', ', $columns).')';

        $parts[] = 'VALUES';
        $parts[] = '('.implode(', ', $this->values).')';

        return $parts;
    }

    /**
     * @param array $parts
     * @return array
     * @throws \Exception
     */
    private function updateRender(array $parts): array
    {
        if (empty($this->from)) {
            throw new \Exception('Aucune table sélectionné');
        }
        $parts[] = $this->generateTabWithAlias($this->from);

        $sets = [];
        foreach ($this->values as $key => $value) {
            $sets[] = "{$key} = {$value}";
        }

        $parts[] = 'SET';
        $parts[] = implode(', ', $sets);

        if (!empty($this->where)) {
            $parts[] = $this->generateCondition($this->where);
        }

        return $parts;
    }

    /**
     * @param array $parts
     * @return array
     * @throws \Exception
     */
    private function deleteRender(array $parts): array
    {
        $parts[] = $this->from[0];

        if (!empty($this->where)) {
            $parts[] = $this->generateCondition($this->where);
        }

        if ($this->limit) {
            [$offset, $limit] = $this->limit;
            $str = "LIMIT {$offset}";
            if ($limit !== null) {
                $str = "LIMIT {$limit}";
            }
            $parts[] = $str;
        }

        return $parts;
    }

    /**
     * @param string $type
     * @throws \Exception
     */
    private function setType(string $type)
    {
        if ($this->type === QueryType::NONE) {
            $this->type = $type;
        } elseif ($this->type !== $type) {
            throw new \Exception('On ne peut pas changer de type de requête en cours de génération');
        }
    }

    /**
     * @param string $from
     */
    private function setFrom(string $from): void
    {
        $this->from = [$from];
    }

    /**
     * @param array $tab
     * @return string
     */
    private function generateTabWithAlias(array $tab): string
    {
        $from = [];
        foreach ($tab as $key => $value) {
            if (is_int($key)) {
                $from[] = $value;
            } else {
                $from[] = $value.' AS '.$key;
            }
        }

        return implode(', ', $from);
    }

    /**
     * @param array $tab
     * @return string
     * @throws \Exception
     */
    private function generateCondition(array $tab): string
    {
        $wheres = [];
        foreach ($tab as $option) {
            if (array_key_exists('group', $option)) {
                $wheres[] = $option['prefix'].' ('.$this->generateCondition($option['group']).')';
            } else {
                if ($option['op'] === 'BETWEEN') {
                    $betwwen = $option['value'];
                    $option['value'] = "{$betwwen[0]} AND {$betwwen[1]}";
                } elseif (in_array($option['op'], ['IN', 'NOT IN'])) {
                    if (is_callable($option['value'])) {
                        $query = Query::makeQuery($this->connection);
                        $option['value']($query);

                        $option['value'] = '('.(string)$query.')';
                    }
                }
                $option = [
                    $option['prefix'],
                    $option['column'],
                    $option['op'],
                    $option['value']
                ];
                $wheres[] = implode(' ', $option);
            }
        }

        return implode(' ', $wheres);
    }

    /**
     * @param array $join
     * @return string
     */
    private function generateJoin(array $join): string
    {
        $joins = [];
        foreach ($join as $value) {
            $joins[] = implode(' ', [
                $value['prefix'],
                $value['table'],
                $value['on']
            ]);
        }

        return implode(' ', $joins);
    }

    /**
     * On ne peut pas faire certaines requête
     * @throws \Exception
     */
    private function turnOffMasterQuery(): void
    {
        if ($this->type !== QueryType::SELECT) {
            throw new \Exception('On ne peut pas faire autre chose qu\'un select dans une sous-requête');
        }
        $this->masterQuery = false;
    }

    /**
     * @param string $column
     * @param string $op
     * @param mixed $value
     * @param null|string $prefix
     * @param bool $escape
     * @return Query
     */
    private function addWhere(string $column, string $op, $value, ?string $prefix, bool $escape): self
    {
        if ($value === null) {
            $value = $op;
            $op = '=';
        }

        if ($escape) {
            $param = ':'.$column;
            $this->params([$param => $value]);

            $value = $param;
        }

        $this->where[] = compact('column', 'op', 'value', 'prefix');
        return $this;
    }

    /**
     * @param string $prefix
     * @param callable $callback
     * @return Query
     * @throws \Exception
     */
    private function whereGroupAdd(string $prefix, callable $callback): self
    {
        $where = [
            'prefix' => $prefix,
            'group' => []
        ];

        $query = Query::makeQuery($this->connection);
        $callback($query);

        $where['group'] = $query->where;
        $this->where[] = $where;
        return $this;
    }

    /**
     * @param string $prefix
     * @param callable $on
     * @return Query
     * @throws \Exception
     */
    private function addJoin(string $prefix, callable $on): self
    {
        $join = [
            'prefix' => $prefix,
            'table' => null,
            'on' => null
        ];

        $query = Query::makeQuery($this->connection);
        $on($query);

        if (empty($query->from)) {
            throw new \Exception('Aucune table sélectionné');
        }
        $join['table'] = $this->generateTabWithAlias($query->from);

        if (empty($query->where)) {
            throw new \Exception('Aucune condition effectué');
        }
        $join['on'] = str_replace('WHERE', 'ON', $this->generateCondition($query->where));

        $this->join[] = $join;
        return $this;
    }
}
