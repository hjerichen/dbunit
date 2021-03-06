<?php declare(strict_types=1);

namespace HJerichen\DBUnit;

use HJerichen\DBUnit\Comparator\DatabaseDatasetComparator;
use HJerichen\DBUnit\DatabaseCleanup\DatabaseCleanerMySQL;
use HJerichen\DBUnit\Dataset\Database\DatabaseDatasetPDO;
use HJerichen\DBUnit\Dataset\Dataset;
use HJerichen\DBUnit\Importer\ImporterPDO;
use HJerichen\DBUnit\Setup\SetupOperation;
use HJerichen\DBUnit\Setup\SetupOperationPDO;
use HJerichen\DBUnit\Teardown\TeardownOperation;
use PDO;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
trait MySQLTestCaseTrait
{
    abstract protected function getDatasetForSetup(): Dataset;

    abstract protected function getDatabase(): PDO;

    protected function setUp(): void
    {
        parent::setUp();
        $dataset = $this->getDatasetForSetup();

        $setupOperation = $this->getSetupOperation();
        $setupOperation->execute($dataset);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $teardownOperation = $this->getTeardownOperation();
        $teardownOperation->execute();
    }

    protected function assertDatasetEqualsCurrent(Dataset $expected): void
    {
        $database = $this->getDatabase();
        $actual = new DatabaseDatasetPDO($database);

        $comparator = new DatabaseDatasetComparator();
        $comparator->assertEquals($expected, $actual);
    }

    private function getSetupOperation(): SetupOperation
    {
        $database = $this->getDatabase();

        $databaseCleaner = new DatabaseCleanerMySQL($database);
        $importer = new ImporterPDO($database);
        return new SetupOperationPDO($databaseCleaner, $importer);
    }

    private function getTeardownOperation(): TeardownOperation
    {
        $database = $this->getDatabase();
        return new DatabaseCleanerMySQL($database);
    }
}
