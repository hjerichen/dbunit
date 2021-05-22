<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Tests\Unit\Classes\Setup;

use Exception;
use HJerichen\DBUnit\DatabaseCleanup\DatabaseCleanerMySQL;
use HJerichen\DBUnit\Dataset\DatasetArray;
use HJerichen\DBUnit\Importer\ImporterPDO;
use HJerichen\DBUnit\Setup\SetupOperationPDO;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class SetupOperationPDOTest extends TestCase
{
    use ProphecyTrait;

    private SetupOperationPDO $setupOperation;
    private ObjectProphecy $database;
    private ObjectProphecy $importer;
    private DatasetArray $dataset;
    private string $databaseName;

    protected function setUp(): void
    {
        parent::setUp();
        $this->database = $this->prophesize(PDO::class);
        $this->importer = $this->prophesize(ImporterPDO::class);
        $this->importer->getDatabase()->willReturn($this->database->reveal());
        $this->dataset = new DatasetArray([]);

        $this->setupDatabaseName();

        $this->setupOperation = new SetupOperationPDO(
            new DatabaseCleanerMySQL($this->database->reveal()),
            $this->importer->reveal(),
        );
    }

    /* TESTS */

    public function testForAllTablesHaveNoData(): void
    {
        $this->setupNoTablesHaveData();

        $this->expectDisableForeignKeyChecks();
        $this->expectImportOfDataset();
        $this->expectEnableForeignKeyChecks();

        $this->setupOperation->execute($this->dataset);
    }

    public function testForSomeTablesHaveData(): void
    {
        $this->setupTablesWithData(['table-1', 'table-2']);

        $this->expectTruncateTables(['table-1', 'table-2']);
        $this->expectDisableForeignKeyChecks(2);
        $this->expectImportOfDataset();
        $this->expectEnableForeignKeyChecks(2);

        $this->setupOperation->execute($this->dataset);
    }

    public function testForImportThrowsException(): void
    {
        $exception = new Exception;

        $this->setupNoTablesHaveData();
        $this->setupImportThrowsException($exception);

        $this->expectDisableForeignKeyChecks();
        $this->expectEnableForeignKeyChecks();
        $this->expectExceptionObject($exception);

        $this->setupOperation->execute($this->dataset);
    }

    /* HELPERS */

    private function setupDatabaseName(): void
    {
        $this->databaseName = 'test';

        $statement = $this->prophesize(PDOStatement::class);
        $statement->fetchColumn()->willReturn($this->databaseName);

        $this->database->query('select database()')->willReturn($statement->reveal());
    }

    private function setupNoTablesHaveData(): void
    {
        $this->setupTablesWithData([]);
    }

    private function setupTablesWithData(array $tables): void
    {
        $statement = $this->prophesize(PDOStatement::class);
        $statement->execute([$this->databaseName])->shouldBeCalledOnce();
        $statement->fetchAll(PDO::FETCH_COLUMN)->willReturn($tables);

        $sql = 'select table_name from information_schema.tables where table_schema = ? and auto_increment > 1';
        $this->database->prepare($sql)->willReturn($statement->reveal());
    }

    private function setupImportThrowsException(Exception $exception): void
    {
        $this->importer->import(Argument::any())->willThrow($exception);
    }

    /** @param string[] $tables */
    private function expectTruncateTables(array $tables): void
    {
        foreach ($tables as $table) {
            $this->database->exec("truncate table `$table`")->shouldBeCalledOnce();
        }
    }

    private function expectEnableForeignKeyChecks(int $count = 1): void
    {
        $this->database->exec('SET foreign_key_checks=1')->shouldBeCalledTimes($count);
    }

    private function expectDisableForeignKeyChecks(int $count = 1): void
    {
        $this->database->exec('SET foreign_key_checks=0')->shouldBeCalledTimes($count);
    }

    private function expectImportOfDataset(): void
    {
        $this->importer->import($this->dataset)->shouldBeCalledOnce();
    }
}