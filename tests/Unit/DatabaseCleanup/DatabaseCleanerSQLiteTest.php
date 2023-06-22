<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Tests\Unit\DatabaseCleanup;

use HJerichen\DBUnit\DatabaseCleanup\DatabaseCleanerSQLite;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class DatabaseCleanerSQLiteTest extends TestCase
{
    use ProphecyTrait;

    private DatabaseCleanerSQLite $databaseCleaner;
    /** @var ObjectProphecy<PDO> */
    private ObjectProphecy $database;

    protected function setUp(): void
    {
        parent::setUp();
        $this->database = $this->prophesize(PDO::class);

        $this->databaseCleaner = new DatabaseCleanerSQLite($this->database->reveal());
    }

    /* TESTS */

    public function test_execute(): void
    {
        $tables = ['table-1', 'table-2'];
        $this->setUpTables($tables);

        $this->expectTruncateTablesWillBeCalled($tables);

        $this->databaseCleaner->execute();
    }

    /* HELPERS */

    /** @param list<string> $tables */
    private function setUpTables(array $tables): void
    {
        $statement = $this->prophesize(PDOStatement::class);
        $statement->fetchAll(PDO::FETCH_COLUMN)->willReturn($tables);

        $sql = <<<SQL
            SELECT 
                name
            FROM 
                sqlite_master
            WHERE 
                type ='table' AND 
                name NOT LIKE 'sqlite_%';
            SQL;
        $this->database->query($sql)->willReturn($statement);
    }

    /** @param list<string> $tables */
    private function expectTruncateTablesWillBeCalled(array $tables): void
    {
        foreach ($tables as $table) {
            /** @noinspection SqlWithoutWhere */
            $sql = "DELETE FROM $table";
            $this->database->exec($sql)->shouldBeCalledOnce();
        }
    }
}
