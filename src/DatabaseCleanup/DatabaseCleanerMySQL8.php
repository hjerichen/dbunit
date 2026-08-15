<?php
declare(strict_types=1);

namespace HJerichen\DBUnit\DatabaseCleanup;

use PDO;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class DatabaseCleanerMySQL8 extends DatabaseCleanerMySQL
{
    /**
     * @return list<string>
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress MoreSpecificReturnType
     */
    protected function getTablesContainingData(): array
    {
        $databaseName = $this->getDatabaseName();

        $tables = $this->getAllTables($databaseName);
        if (empty($tables)) {
            return [];
        }

        $mapping = static function (string $table) use ($databaseName): string {
            /** @noinspection SqlRedundantLimit */
            return "SELECT '$table' AS table_name WHERE EXISTS (SELECT 1 FROM `$databaseName`.`$table` LIMIT 1)";
        };

        $selects = array_map(callback: $mapping, array: $tables);
        $sql = implode(' UNION ALL ', $selects);
        return $this->database->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    }

    private function getAllTables(string $databaseName): array
    {
        $sql = 'SELECT table_name FROM information_schema.tables WHERE table_schema = ? AND table_type = "BASE TABLE"';
        $stmt = $this->database->prepare($sql);
        $stmt->execute([$databaseName]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}