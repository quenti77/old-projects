<?php

namespace PHQ\Database;

use PHQ\Database\PDO\Statement;

/**
 * Interface IConnection
 * @package PHQ\Database
 */
interface IConnection
{
    /**
     * Execute une requête avec la connexion interne
     *
     * @param string $statement
     * @param array $params
     * @return Statement L'objet représentant les résultats
     */
    public function request(string $statement, array $params = []): Statement;

    /**
     * Démarre un système de transaction qui permet de faire des modifs et
     * de revenir en arrière si jamais une erreur survient.
     * @return void
     */
    public function startTransaction();

    /**
     * Permet de persister les changements fait avec les précédantes
     * requête après avoir lancer un startTransaction
     * @return void
     */
    public function commit();

    /**
     * Permet de revenir en arrière sur les changements fait avec les
     *  précédantes requête après avoir lancer un startTransaction
     * @return void
     */
    public function rollback();

    /**
     * Récupère le dernière id sur une
     * table avec un auto increment
     *
     * @return int
     */
    public function lastId(): int;
}
