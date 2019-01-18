<?php

namespace Tests\PHQ\Database;

use PHPUnit\Framework\TestCase;
use PHQ\Database\Dsn;

class DsnTest extends TestCase
{

    /**
     * Permet de savoir si l'objet est bien construit
     */
    public function testIfObjectIsConstruct()
    {
        $dsn = $this->generateDsn();
        $this->assertInstanceOf(Dsn::class, $dsn);
    }

    /**
     * Test avec un type de base MySQL
     * @depends testIfObjectIsConstruct
     */
    public function testGenerateMysqlType(): void
    {
        $dsn = $this->generateDsn();
        $dsnString = $dsn->generate();
        $dsnExpect = 'mysql:host=localhost;dbname=tests;port=3306';

        $this->assertSame(
            $dsnExpect,
            $dsnString
        );
    }

    /**
     * Test avec un type sqlite et un fichier
     * @depends testIfObjectIsConstruct
     */
    public function testGenerateSqliteFile(): void
    {
        $file = '/base/tests.sqlite';
        $dsn = $this->generateDsn('sqlite', $file);

        $dsnString = $dsn->generate();
        $dsnExpect = "sqlite:{$file}";

        $this->assertSame(
            $dsnExpect,
            $dsnString
        );
    }

    /**
     * Test avec un type sqlite et un fichier
     * @depends testIfObjectIsConstruct
     */
    public function testGenerateSqliteMemory(): void
    {
        $file = ':memory:';
        $dsn = $this->generateDsn('sqlite', $file);

        $dsnString = $dsn->generate();
        $dsnExpect = "sqlite:{$file}";

        $this->assertSame(
            $dsnExpect,
            $dsnString
        );
    }

    /**
     * @param string $type
     * @param string $host
     * @param string $name
     * @param int $port
     * @param string $charset
     * @return Dsn
     */
    private function generateDsn(
        string $type = 'mysql',
        string $host = 'localhost',
        string $name = 'tests',
        int $port = 3306,
        string $charset = 'utf8'): Dsn
    {
        return new Dsn($type, $host, $name, $port, $charset);
    }
}
