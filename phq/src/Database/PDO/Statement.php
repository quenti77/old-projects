<?php

namespace PHQ\Database\PDO;

use PDOStatement;
use PHQ\Database\IStatement;

class Statement implements IStatement
{
    /**
     * Notre objet représentant la requête
     *
     * @var PDOStatement $statement
     */
    private $statement;

    public function __construct(PDOStatement $statement)
    {
        $this->statement = $statement;
    }

    /**
     * Lie une valeur à un paramètre dans la requête
     * "SELECT * FROM users WHERE id = :id"
     * $obj->bind(":id", 1, PARAM_INT);
     *
     * @param string $name
     * @param mixed $value
     * @param int $type
     * @return bool
     */
    public function bind(string $name, $value, int $type): bool
    {
        return $this->statement->bindValue($name, $value, $type);
    }

    /**
     * Exécute la requête. Ne prends aucun paramètre contrairement à PDO
     *
     * @return bool
     */
    public function execute(): bool
    {
        return $this->statement->execute();
    }

    /**
     * Récupère une ligne du jeu de résultat
     *
     * @return bool|array
     */
    public function fetch()
    {
        return $this->statement->fetch();
    }

    /**
     * Récupère toutes les ligne du jeu de résultat
     *
     * @return array
     */
    public function fetchAll(): array
    {
        return $this->statement->fetchAll();
    }
}
