<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Dataset\Database;

use HJerichen\DBUnit\Dataset\Table;
use PDO;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 * @psalm-import-type ValueSet from Table
 */
class DatabaseDatasetPDO implements DatabaseDataset
{
    /** @var array<string, list<string>> */
    private array $tableColumns = [];

    public function __construct(
        private readonly PDO $database
    ) {
    }

    /** @param list<string> $columns */
    public function setTableColumns(string $tableName, array $columns): void
    {
        $this->tableColumns[$tableName] = $columns;
    }

    public function getTables(): array
    {
        $tableNames = array_keys($this->tableColumns);
        return array_map($this->buildTable(...), $tableNames);
    }

    private function buildTable(string $tableName): Table
    {
        $valueSets = $this->getValueSetsForTable($tableName);
        return new Table($tableName, $valueSets);
    }

    /**
     * @return list<ValueSet>
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress MoreSpecificReturnType
     */
    private function getValueSetsForTable(string $tableName): array
    {
        $sql = $this->buildSQLForTable($tableName);
        return $this->database->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function buildSQLForTable(string $tableName): string
    {
        $columns = $this->tableColumns[$tableName];
        $columnsSQL = '`' . implode('`, `', $columns) . '`';
        return "SELECT $columnsSQL FROM `$tableName`";
    }
}