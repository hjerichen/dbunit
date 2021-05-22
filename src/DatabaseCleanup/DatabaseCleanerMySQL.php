<?php declare(strict_types=1);

namespace HJerichen\DBUnit\DatabaseCleanup;

use PDO;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class DatabaseCleanerMySQL implements DatabaseCleaner
{
    private PDO $database;

    public function __construct(PDO $database)
    {
        $this->database = $database;
    }

    public function execute(): void
    {
        $tables = $this->getTablesContainingData();
        if (count($tables) === 0) return;

        try {
            $this->disableForeignKeyChecks();
            $this->truncateTables($tables);
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

    private function getDatabaseName(): string
    {
        return $this->database->query('select database()')->fetchColumn();
    }

    private function enableForeignKeyChecks(): void
    {
        $this->database->exec('SET foreign_key_checks=1');
    }

    private function disableForeignKeyChecks(): void
    {
        $this->database->exec('SET foreign_key_checks=0');
    }
}