<?php

namespace PHQ\Database\PDO;

use DateTime;
use PDO;
use PHQ\Database\Dsn;
use PHQ\Database\IConnection;

/**
 * Class Connection
 * @package PHQ\Database\PDO
 */
class Connection extends PDO implements IConnection
{
    const TYPE = [
        'integer' => parent::PARAM_INT,
        'boolean' => parent::PARAM_BOOL
    ];

    /**
     * @var bool  $isTransaction
     */
    private $isTransaction;

    /**
     * Connection constructor.
     * @param Dsn $dsn
     * @param string $username
     * @param string $passwd
     * @param string $charset
     * @param array $options
     */
    public function __construct(
        Dsn $dsn,
        string $username,
        string $passwd,
        string $charset = 'UTF8',
        array $options = []
    ) {
        parent::__construct($dsn->generate(), $username, $passwd, $options);
        parent::setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        parent::setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        if (!$dsn->isSqlite()) {
            parent::exec("SET NAMES '{$charset}'");
        }

        $this->isTransaction = false;
    }

    /**
     * Execute une requête avec la connexion interne
     *
     * @param string $statement
     * @param array $params
     * @return Statement L'objet représentant les résultats
     */
    public function request(string $statement, array $params = []): Statement
    {
        $request = new Statement($this->prepare($statement));

        foreach ($params as $field => $value) {
            $paramType = gettype($value);
            $bindType = parent::PARAM_STR;

            if ($value instanceof DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            } elseif (array_key_exists($paramType, self::TYPE)) {
                $bindType = self::TYPE[$paramType];
            } elseif ($value === null) {
                $bindType = parent::PARAM_NULL;
            }

            $request->bind($field, $value, $bindType);
        }

        $request->execute();
        return $request;
    }

    /**
     * Démarre un système de transaction qui permet de faire des modifs et
     * de revenir en arrière si jamais une erreur survient.
     * @return void
     */
    public function startTransaction()
    {
        if ($this->isTransaction) {
            return;
        }
        $this->isTransaction = true;

        try {
            parent::setAttribute(parent::ATTR_AUTOCOMMIT, 0);
        } catch (\Exception $e) {
            //
        }
        parent::beginTransaction();
    }

    /**
     * Permet de persister les changements fait avec les précédantes
     * requête après avoir lancer un startTransaction
     * @return void
     */
    public function commit()
    {
        if (!$this->isTransaction) {
            return;
        }
        $this->isTransaction = false;

        try {
            parent::setAttribute(parent::ATTR_AUTOCOMMIT, 1);
        } catch (\Exception $e) {
            //
        }
        parent::commit();
    }

    /**
     * Permet de revenir en arrière sur les changements fait avec les
     *  précédantes requête après avoir lancer un startTransaction
     * @return void
     */
    public function rollback()
    {
        if (!$this->isTransaction) {
            return;
        }
        $this->isTransaction = false;

        try {
            parent::setAttribute(parent::ATTR_AUTOCOMMIT, 1);
        } catch (\Exception $e) {
            //
        }
        parent::rollBack();
    }

    /**
     * Récupère le dernière id sur une
     * table avec un auto increment
     *
     * @return int
     */
    public function lastId(): int
    {
        return parent::lastInsertId();
    }
}
