<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Setup;

use HJerichen\DBUnit\DatabaseCleanup\DatabaseCleaner;
use HJerichen\DBUnit\Dataset\Dataset;
use HJerichen\DBUnit\Importer\ImporterPDO;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class SetupOperationPDO implements SetupOperation
{
    private DatabaseCleaner $databaseCleaner;
    private ImporterPDO $importer;

    public function __construct(DatabaseCleaner $databaseCleaner, ImporterPDO $importer)
    {
        $this->databaseCleaner = $databaseCleaner;
        $this->importer = $importer;
    }

    public function execute(Dataset $dataset): void
    {
        $this->cleanupDatabase();
        $this->importDataset($dataset);
    }

    private function cleanupDatabase(): void
    {
        $this->databaseCleaner->execute();
    }

    private function importDataset(Dataset $dataset): void
    {
        try {
            $this->disableForeignKeyChecks();
            $this->importer->import($dataset);
        } finally {
            $this->enableForeignKeyChecks();
        }
    }

    private function enableForeignKeyChecks(): void
    {
        $database = $this->importer->getDatabase();
        $database->exec('SET foreign_key_checks=1');
    }

    private function disableForeignKeyChecks(): void
    {
        $database = $this->importer->getDatabase();
        $database->exec('SET foreign_key_checks=0');
    }
}