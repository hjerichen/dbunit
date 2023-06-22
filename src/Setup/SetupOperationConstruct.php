<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Setup;

use HJerichen\DBUnit\DatabaseCleanup\DatabaseCleaner;
use HJerichen\DBUnit\Dataset\Dataset;
use HJerichen\DBUnit\ForeignKey\ForeignKeyHandler;
use HJerichen\DBUnit\Importer\Importer;
use HJerichen\DBUnit\StrictMode\StrictModeHandler;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class SetupOperationConstruct implements SetupOperation
{
    public function __construct(
        private readonly StrictModeHandler $strictModeHandler,
        private readonly ForeignKeyHandler $foreignKeyHandler,
        private readonly DatabaseCleaner $databaseCleaner,
        private readonly Importer $importer,
    ) {
    }

    public function execute(Dataset $dataset): void
    {
        try {
            $this->disableStrictMode();
            $this->disableForeignKeyChecks();
            $this->cleanupDatabase();
            $this->importDataset($dataset);
        } finally {
            $this->enableForeignKeyChecks();
            $this->restoreStrictMode();
        }
    }

    private function cleanupDatabase(): void
    {
        $this->databaseCleaner->execute();
    }

    private function importDataset(Dataset $dataset): void
    {
        $this->importer->import($dataset);
    }

    private function enableForeignKeyChecks(): void
    {
        $this->foreignKeyHandler->enableForeignKeyCheck();
    }

    private function disableForeignKeyChecks(): void
    {
        $this->foreignKeyHandler->disableForeignKeyCheck();
    }

    private function disableStrictMode(): void
    {
        $this->strictModeHandler->disableStrictMode();
    }

    private function restoreStrictMode(): void
    {
        $this->strictModeHandler->restoreStrictMode();
    }
}