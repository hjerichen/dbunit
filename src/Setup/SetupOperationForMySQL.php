<?php

namespace HJerichen\DBUnit\Setup;

use HJerichen\DBUnit\Dataset\Dataset;
use HJerichen\DBUnit\Importer\ImporterPDO;
use PDO;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class SetupOperationForMySQL
{
    private PDO $database;
    private ImporterPDO $importer;

    public function __construct(PDO $database, ImporterPDO $importer)
    {
        $this->database = $database;
        $this->importer = $importer;
    }

    public function execute(Dataset $dataset): void
    {
        $this->cleanupDatabase();
        $this->importDataset($dataset);
    }

    private function cleanupDatabase(): void
    {
        $tables = $this->getTablesContainingData();
        $this->truncateTables($tables);
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

    /** @return string[] */
    private function getTablesContainingData(): array
    {
        $databaseName = $this->getDatabaseName();

        $sql = 'select table_name from information_schema.tables where table_schema = ? and auto_increment > 1';
        $statement = $this->database->prepare($sql);
        $statement->execute([$databaseName]);
        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    /** @param string[] $tables */
    private function truncateTables(array $tables): void
    {
        foreach ($tables as $table) {
            $this->database->exec("truncate table {$table}");
        }
    }

    private function enableForeignKeyChecks(): void
    {
        $this->database->exec('SET foreign_key_checks=1');
    }

    private function disableForeignKeyChecks(): void
    {
        $this->database->exec('SET foreign_key_checks=0');
    }

    private function getDatabaseName(): string
    {
        return $this->database->query('select database()')->fetchColumn();
    }
}