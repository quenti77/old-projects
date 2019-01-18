<?php

namespace PHQ\Database;

/**
 * Class Dsn
 * @package PHQ\Database
 */
class Dsn
{
    /**
     * @var string $type
     */
    private $type;

    /**
     * @var string $host
     */
    private $host;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var int $port
     */
    private $port;

    /**
     * @var string $charset
     */
    private $charset;

    /**
     * Dsn constructor.
     * @param string $type
     * @param string $host
     * @param string|null $name
     * @param int $port
     */
    public function __construct(string $type, string $host, string $name = null, int $port = 3306)
    {
        $this->type = $type;
        $this->host = $host;
        $this->name = $name;
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function generate()
    {
        $dsn = "{$this->type}:";
        if ($this->isSqlite()) {
            $dsn .= "{$this->host}";
            return $dsn;
        }

        $dsn .= "host={$this->host};dbname={$this->name};port={$this->port}";
        return $dsn;
    }

    /**
     * @return bool
     */
    public function isSqlite(): bool
    {
        return strpos($this->type, 'sqlite') !== false;
    }
}
