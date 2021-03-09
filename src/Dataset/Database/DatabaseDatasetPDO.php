<?php


namespace HJerichen\DBUnit\Dataset\Database;

use HJerichen\DBUnit\Dataset\Table;
use PDO;

class DatabaseDatasetPDO implements DatabaseDataset
{
    private PDO $database;
    private array $tableColumns = [];

    public function __construct(PDO $database)
    {
        $this->database = $database;
    }

    public function setTableColumns(string $tableName, array $columns): void
    {
        $this->tableColumns[$tableName] = $columns;
    }

    public function getTables(): array
    {
        $tableNames = array_keys($this->tableColumns);
        return array_map([$this, 'buildTable'], $tableNames);
    }

    private function buildTable(string $tableName): Table
    {
        $valueSets = $this->getValueSetsForTable($tableName);
        return new Table($tableName, $valueSets);
    }

    private function getValueSetsForTable(string $tableName): array
    {
        $sql = $this->buildSQLForTable($tableName);
        return $this->database->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function buildSQLForTable($tableName): string
    {
        $columns = $this->tableColumns[$tableName];
        $columnsSQL = '`' . implode('`, `', $columns) . '`';
        return "SELECT {$columnsSQL} FROM {$tableName}";
    }
}