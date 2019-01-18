<?php

namespace PHQ\Database\QueryBuilder;

/**
 * Class QueryType
 * @package PHQ\Database\QueryBuilder
 */
class QueryType
{
    const NONE = '';
    const SELECT = 'SELECT';
    const INSERT = 'INSERT INTO';
    const UPDATE = 'UPDATE';
    const DELETE = 'DELETE FROM';
}
