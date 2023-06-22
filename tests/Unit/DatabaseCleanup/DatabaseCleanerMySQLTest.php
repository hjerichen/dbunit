<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Tests\Unit\DatabaseCleanup;

use Exception;
use HJerichen\DBUnit\DatabaseCleanup\DatabaseCleanerMySQL;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use RuntimeException;

class DatabaseCleanerMySQLTest extends TestCase
{
    use ProphecyTrait;

    private DatabaseCleanerMySQL $databaseCleaner;
    /** @var ObjectProphecy<PDO> */
    private ObjectProphecy $database;

    private string $databaseName;

    protected function setUp(): void
    {
        parent::setUp();
        $this->database = $this->prophesize(PDO::class);

        $this->setUpDatabaseName('db-1');

        $this->databaseCleaner = new DatabaseCleanerMySQL($this->database->reveal());
    }

    /* TESTS */

    public function test_execute(): void
    {
        $tables = ['table-1', 'table-2'];
        $this->setUpTablesWithData($tables);

        $this->expectDisableForeignKeyChecksWillBeCalled();
        $this->expectTruncateTablesWillBeCalled($tables);
        $this->expectEnableForeignKeyChecksWillBeCalled();

        $this->databaseCleaner->execute();
    }

    public function test_execute_withExceptionThrown(): void
    {
        $exception = new RuntimeException('test');
        $tables = ['table-1', 'table-2'];

        $this->setUpTablesWithData($tables);
        $this->setUpTruncateTableWillThrowException($tables[0], $exception);

        $this->expectDisableForeignKeyChecksWillBeCalled();
        $this->expectEnableForeignKeyChecksWillBeCalled();
        $this->expectExceptionObject($exception);

        $this->databaseCleaner->execute();
    }

    /* HELPERS */

    private function setUpDatabaseName(string $databaseName): void
    {
        $this->databaseName = $databaseName;

        $statement = $this->prophesize(PDOStatement::class);
        $statement->fetchColumn()->willReturn($this->databaseName);

        $sql = 'select database()';
        $this->database->query($sql)->willReturn($statement);
    }

    /** @param list<string> $tables */
    private function setUpTablesWithData(array $tables): void
    {
        $statement = $this->prophesize(PDOStatement::class);
        $statement->fetchAll(PDO::FETCH_COLUMN)->willReturn($tables);

        $sql = 'select table_name from information_schema.tables where table_schema = ? and auto_increment > 1';
        $this->database->prepare($sql)->willReturn($statement);

        $statement->execute([$this->databaseName])->shouldBeCalledOnce();
    }

    private function setUpTruncateTableWillThrowException(string $table, Exception $exception): void
    {
        $this->database->exec("truncate table `$table`")->willThrow($exception);
    }

    /** @param list<string> $tables */
    private function expectTruncateTablesWillBeCalled(array $tables): void {
        foreach ($tables as $table) {
            $this->database->exec("truncate table `$table`")->shouldBeCalledOnce();
        }
    }

    private function expectEnableForeignKeyChecksWillBeCalled(): void
    {
        $this->database->exec('SET foreign_key_checks=1')->shouldBeCalledOnce();
    }

    private function expectDisableForeignKeyChecksWillBeCalled(): void
    {
        $this->database->exec('SET foreign_key_checks=0')->shouldBeCalledOnce();
    }
}
