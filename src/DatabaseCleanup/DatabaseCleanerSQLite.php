<?php declare(strict_types=1);

namespace HJerichen\DBUnit\DatabaseCleanup;

use PDO;

class DatabaseCleanerSQLite implements DatabaseCleaner
{
    public function __construct(
        private readonly PDO $database
    ) {
    }

    public function execute(): void
    {
        $tables = $this->getTables();
        if (count($tables) === 0) return;

        $this->truncateTables($tables);
    }

    /** @return list<string> */
    private function getTables(): array
    {
        $sql = <<<SQL
            SELECT 
                name
            FROM 
                sqlite_master
            WHERE 
                type ='table' AND 
                name NOT LIKE 'sqlite_%';
            SQL;
        /** @var list<string> $result */
        $result = $this->database->query($sql)->fetchAll(PDO::FETCH_COLUMN);
        return $result;
    }

    /** @param list<string> $tables  */
    private function truncateTables(array $tables): void
    {
        foreach ($tables as $table) {
            /** @noinspection SqlWithoutWhere */
            $sql = "DELETE FROM $table";
            $this->database->exec($sql);
        }
    }
}
