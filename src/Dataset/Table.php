<?php

namespace HJerichen\DBUnit\Dataset;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class Table
{
    private string $name;
    private array $valueSets;

    public function __construct(string $name, array $valueSets)
    {
        $this->name = $name;
        $this->valueSets = $valueSets;
    }

    public function setValueSets(array $valueSets): void
    {
        $this->valueSets = $valueSets;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColumns(): array
    {
        $valueSetColumns = [];
        foreach ($this->valueSets as $valueSet) {
            $valueSetColumns[] = array_keys($valueSet);
        }
        $columns = array_merge(...$valueSetColumns);
        $columns = array_values(array_unique($columns));
        return $columns;
    }

    public function getValueSets(): array
    {
        return $this->valueSets;
    }

    public function getNormalizedValueSets(): array
    {
        return array_map([$this, 'normalizeValueSet'], $this->valueSets);
    }

    private function normalizeValueSet(array $valueSet): array
    {
        $missingColumns = array_diff($this->getColumns(), array_keys($valueSet));
        foreach ($missingColumns as $column) {
            $valueSet[$column] = null;
        }
        return $valueSet;
    }
}