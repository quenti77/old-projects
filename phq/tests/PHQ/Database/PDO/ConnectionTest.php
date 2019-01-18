<?php

namespace Tests\PHQ\Database\PDO;

use PDO;
use PHPUnit\Framework\TestCase;
use PHQ\Database\Dsn;
use PHQ\Database\PDO\Connection;
use PHQ\Database\PDO\Statement;

class ConnectionTest extends TestCase
{
    /**
     * @var Connection $db;
     */
    private $db;

    /**
     * Permet de savoir si l'objet est bien construit
     */
    public function testIfObjectIsConstruct()
    {
        $this->assertInstanceOf(Connection::class, $this->db);
    }

    /**
     * Permet de tester le lancement d'une requête
     * @depends testIfObjectIsConstruct
     */
    public function testRequestExecute()
    {
        $statement = $this->generateStatement();
        $this->assertInstanceOf(Statement::class, $statement);
    }

    /**
     * Permet de savoir si l'objet est bien construit
     * @depends testRequestExecute
     */
    public function testRequestExecuteWithParams()
    {
        $statement = $this->generateStatement(
            'SELECT * FROM contacts
                    WHERE id = :id OR nickname = :nickname OR
                        email = :email OR phone = :phone',
            [
                ':id' => 1,
                ':nickname' => 'name',
                ':email' => new \DateTime(),
                ':phone' => null
            ]
        );
        $this->assertInstanceOf(Statement::class, $statement);
    }

    /**
     * Permet de tester le lancement d'une requête
     * @depends testIfObjectIsConstruct
     */
    public function testRequestGetLastInsertId()
    {
        $statement = $this->generateStatement();
        $this->assertSame(0, $this->db->lastId());
    }

    /**
     * Permet de tester que les requêtes on bien été lancé après le commit
     * et que les données sont bien persistées
     * @depends testRequestGetLastInsertId
     */
    public function testCommitTransaction()
    {
        $this->db->startTransaction();
        $this->db->startTransaction(); // N'a aucune incidence si on le refait

        $this->db->request('
            INSERT INTO contacts (id, nickname, email, phone)
            VALUES (1, "pseudo1", "pseudo.un@local.dev", "33102030405")');

        $this->db->request('
            INSERT INTO contacts (id, nickname, email, phone)
            VALUES (2, "pseudo1", "pseudo.un@local.dev", "33102030406")');

        $this->db->commit();
        $this->db->commit(); // N'a aucune incidence si on le refait

        // On vérifie que l'on à bien 2 lignes avec le nickname qui vaut "pseudo1"
        $val = $this->db->request('SELECT COUNT(*) AS nb FROM contacts')->fetch();
        $this->assertSame(2, intval($val['nb']));
    }

    /**
     * Permet de tester que les requêtes on bien été lancé après le commit
     * mais que les données ne sont pas persisté
     * @depends testCommitTransaction
     */
    public function testRollbackTransaction()
    {
        $this->db->startTransaction();
        $this->db->startTransaction(); // N'a aucune incidence si on le refait

        $this->db->request('
            INSERT INTO contacts (id, nickname, email, phone)
            VALUES (1, "pseudo1", "pseudo.un@local.dev", "33102030405")');

        $this->db->request('
            INSERT INTO contacts (id, nickname, email, phone)
            VALUES (2, "pseudo1", "pseudo.un@local.dev", "33102030406")');

        $this->db->rollback();
        $this->db->rollback(); // N'a aucune incidence si on le refait

        // On vérifie que l'on à bien 2 lignes avec le nickname qui vaut "pseudo1"
        $val = $this->db->request('SELECT COUNT(*) AS nb FROM contacts')->fetch();
        $this->assertSame(0, intval($val['nb']));
    }

    public function setUp()
    {
        parent::setUp();
        $this->db = new Connection(new Dsn('sqlite', ':memory:'), '', '');

        $this->db->request('
            CREATE TABLE contacts (
                id integer PRIMARY KEY,
                nickname text NOT NULL,
                email text NOT NULL,
                phone text NOT NULL UNIQUE
            )');
    }

    /**
     * @param string $query
     * @param array $params
     * @return Statement
     */
    private function generateStatement(
        string $query = 'SELECT * FROM contacts WHERE id = :id',
        array $params = []): Statement
    {
        return $this->db->request($query, $params);
    }
}
