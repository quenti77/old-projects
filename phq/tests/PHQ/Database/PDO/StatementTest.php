<?php

namespace Tests\PHQ\Database\PDO;

use PDO;
use PHPUnit\Framework\TestCase;
use PHQ\Database\PDO\Statement;

class StatementTest extends TestCase
{
    /**
     * @var PDO $db;
     */
    private $db;

    /**
     * Permet de savoir si l'objet est bien construit
     */
    public function testIfObjectIsConstruct()
    {
        $statement = $this->generateStatement();
        $this->assertInstanceOf(Statement::class, $statement);
    }

    /**
     * Test le binding de variable
     * @depends testIfObjectIsConstruct
     */
    public function testBindValueResult()
    {
        $statement = $this->generateStatement();

        $this->assertTrue($statement->bind(':id', 1, PDO::PARAM_INT));
        $this->assertTrue($statement->bind(':unknown', 1, PDO::PARAM_INT));
    }

    /**
     * Test le binding de variable
     * @depends testBindValueResult
     */
    public function testExecuteStatement()
    {
        $statement = $this->generateStatement();
        $statement->bind(':id', 1, PDO::PARAM_INT);

        $this->assertTrue($statement->execute());
    }

    /**
     * Test la récupération ligne par ligne
     * @depends testExecuteStatement
     */
    public function testFetchData()
    {
        $this->db->query('
            INSERT INTO contacts (id, nickname, email, phone)
                VALUES (1, \'first\', \'first@phq.localhost\', \'0102030405\')');

        $statement = $this->generateStatement();
        $statement->bind(':id', 1, PDO::PARAM_INT);
        $statement->execute();

        $line = $statement->fetch();
        $this->assertNotFalse($line);
        $this->assertTrue(is_array($line));

        $line = $statement->fetch();
        $this->assertFalse($line);
    }

    /**
     * Test de la récupération de toute les lignes
     * @depends testExecuteStatement
     */
    public function testFetchAllData()
    {
        $this->db->query('
            INSERT INTO contacts (id, nickname, email, phone)
                VALUES (1, \'first\', \'first@phq.localhost\', \'0102030405\')');

        $statement = $this->generateStatement();
        $statement->bind(':id', 1, PDO::PARAM_INT);
        $statement->execute();

        $line = $statement->fetchAll();
        $this->assertCount(1, $line);

        $line = $statement->fetchAll();
        $this->assertCount(0, $line);
    }

    public function setUp()
    {
        parent::setUp();
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $this->db->query('
            CREATE TABLE contacts (
                id integer PRIMARY KEY,
                nickname text NOT NULL,
                email text NOT NULL,
                phone text NOT NULL UNIQUE
            )');
    }

    /**
     * @param string $query
     * @return Statement
     */
    private function generateStatement(string $query = 'SELECT * FROM contacts WHERE id = :id'): Statement
    {
        return new Statement($this->db->prepare($query));
    }
}
