<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Dataset\Converter\SQL;

use HJerichen\DBUnit\Dataset\Table;
use HJerichen\DBUnit\Dataset\TableSplitter;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 * @psalm-import-type ValueSet from Table
 */
class TableToSqlConverter
{
    private TableSplitter $tableSplitter;

    public function __construct()
    {
        $this->tableSplitter = new TableSplitter;
    }

    /**
     * @param Table $table
     * @return SqlQuerySet[]
     */
    public function getQuerySetsForTable(Table $table): array
    {
        $tables = $this->tableSplitter->splitTable($table);
        return $this->buildQuerySetsForTables($tables);
    }

    /**
     * @param Table[] $tables
     * @return SqlQuerySet[]
     */
    private function buildQuerySetsForTables(array $tables): array
    {
        $querySets = [];
        foreach ($tables as $table) {
            $querySet = new SqlQuerySet();
            $querySet->query = $this->buildQueryForTable($table);
            $querySet->parameters = $this->buildParametersForTable($table);
            $querySets[] = $querySet;
        }
        return $querySets;
    }

    private function buildQueryForTable(Table $table): string
    {
        $columns = '(`' . implode('`, `', $table->getColumns()) . '`)';
        $valueSets = implode(', ', $this->buildValueSetsForTable($table));
        return "INSERT INTO `{$table->getName()}` $columns VALUES $valueSets;";
    }

    /**
     * @param Table $table
     * @return list<string>
     */
    private function buildValueSetsForTable(Table $table): array
    {
        $valueSets = [];
        foreach ($table->getValueSets() as $index => $valueSet) {
            $columns = array_keys($valueSet);
            $valueSets[] = '(' . implode(', ', $this->buildValueNamesForValueSet($columns, $index)) . ')';
        }
        return $valueSets;
    }

    /**
     * @param list<string> $columns
     * @param int $index
     * @return list<string>
     */
    private function buildValueNamesForValueSet(array $columns, int $index): array
    {
        $values = [];
        foreach ($columns as $column) {
            $values[] = ":{$column}_$index";
        }
        return $values;
    }

    private function buildParametersForTable(Table $table): array
    {
        $valueNames = $this->buildAllValueNamesForTable($table);
        $valueSets = array_map('array_values', $table->getValueSets());
        $values = array_merge(...$valueSets);
        return array_combine($valueNames, $values);
    }

    /**
     * @param Table $table
     * @return string[]
     */
    private function buildAllValueNamesForTable(Table $table): array
    {
        $valueNames = [];
        foreach ($table->getValueSets() as $index => $valueSet) {
            $columns = array_keys($valueSet);
            $valueNames[] = $this->buildValueNamesForValueSet($columns, $index);
        }
        return array_merge(...$valueNames);
    }
}