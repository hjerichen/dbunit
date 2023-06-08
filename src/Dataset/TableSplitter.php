<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Dataset;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 * @psalm-import-type ValueSet from Table
 */
class TableSplitter
{
    private Table $table;

    /** @var list<Table> */
    private array $newTables = [];
    /** @var array<string,list<ValueSet>> */
    private array $groupedValueSets = [];

    /**
     * @param Table $table
     * @return list<Table>
     */
    public function splitTable(Table $table): array
    {
        $this->table = $table;

        try {
            $this->groupValueSets();
            $this->buildNewTables();
            return $this->newTables;
        } finally {
            $this->cleanup();
        }
    }

    private function groupValueSets(): void
    {
        foreach ($this->table->getValueSets() as $valueSet) {
            $groupKey = implode(',', array_keys($valueSet));
            $this->groupedValueSets[$groupKey][] = $valueSet;
        }
    }

    private function buildNewTables(): void
    {
        foreach ($this->groupedValueSets as $valueSets) {
            $this->newTables[] = new Table($this->table->getName(), $valueSets);
        }
    }

    private function cleanup(): void
    {
        unset($this->table);
        $this->newTables = [];
        $this->groupedValueSets = [];
    }
}