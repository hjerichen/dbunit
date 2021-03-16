<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Dataset;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class TableSorter
{
    public function sortTable(Table $table): void
    {
        $valueSets = $table->getNormalizedValueSets();
        $valueSets = $this->sortColumns($valueSets);
        $valueSets = $this->sortValueSets($valueSets);
        $table->setValueSets($valueSets);
    }

    private function sortColumns(array $valueSets): array
    {
        foreach ($valueSets as &$valueSet) {
            uksort($valueSet, [$this, 'compareColumnNames']);
        }
        return $valueSets;
    }

    private function sortValueSets(array $valueSets): array
    {
        usort($valueSets, [$this, 'compareValueSets']);
        return $valueSets;
    }

    private function compareColumnNames(string $name1, string $name2): int
    {
        if ($name1 === 'id') return -1;
        if ($name2 === 'id') return 1;
        return strcmp($name1, $name2);
    }

    private function compareValueSets(array $valueSet1, array $valueSet2): int
    {
        foreach ($valueSet1 as $column => $value1) {
            $value2 = $valueSet2[$column];
            $compare = $this->compareValues($value1, $value2);
            if ($compare !== 0) return $compare;
        }
        return 0;
    }

    private function compareValues($value1, $value2): int
    {
        if (is_numeric($value1) || is_numeric($value2)) {
            return $value1 - $value2;
        }
        return strcmp($value1, $value2);
    }
}