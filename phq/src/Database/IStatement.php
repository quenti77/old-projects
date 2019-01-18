<?php

namespace PHQ\Database;

/**
 * Interface IStatement
 * @package PHQ\Database
 */
interface IStatement
{
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
    public function bind(string $name, $value, int $type): bool;

    /**
     * Exécute la requête. Ne prends aucun paramètre contrairement à PDO
     *
     * @return bool
     */
    public function execute(): bool;

    /**
     * Récupère une ligne du jeu de résultat
     *
     * @return bool|array
     */
    public function fetch();

    /**
     * Récupère toutes les ligne du jeu de résultat
     *
     * @return array
     */
    public function fetchAll(): array;
}
