<?php declare(strict_types=1);

namespace HJerichen\DBUnit\DatabaseCleanup;

use HJerichen\DBUnit\ForeignKey\ForeignKeyHandler;
use HJerichen\DBUnit\ForeignKey\ForeignKeyHandlerMySQL;
use PDO;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class DatabaseCleanerMySQL implements DatabaseCleaner
{
    private readonly ForeignKeyHandler $foreignKeyHandler;

    public function __construct(
        private readonly PDO $database
    ) {
        $this->foreignKeyHandler = new ForeignKeyHandlerMySQL($this->database);
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

    /**
     * @return list<string>
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress MoreSpecificReturnType
     */
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
            $this->database->exec("truncate table `$table`");
        }
    }

    /**
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    private function getDatabaseName(): string
    {
        return $this->database->query('select database()')->fetchColumn();
    }

    private function enableForeignKeyChecks(): void
    {
        $this->foreignKeyHandler->enableForeignKeyCheck();
    }

    private function disableForeignKeyChecks(): void
    {
        $this->foreignKeyHandler->disableForeignKeyCheck();
    }
}