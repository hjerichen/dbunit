<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Dataset;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 * @psalm-import-type ValueSet from Table
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

    /**
     * @param list<ValueSet> $valueSets
     * @return list<ValueSet>
     */
    private function sortColumns(array $valueSets): array
    {
        foreach ($valueSets as &$valueSet) {
            uksort($valueSet, $this->compareColumnNames(...));
        }
        return $valueSets;
    }

    /**
     * @param list<ValueSet> $valueSets
     * @return list<ValueSet>
     */
    private function sortValueSets(array $valueSets): array
    {
        usort($valueSets, $this->compareValueSets(...));
        return $valueSets;
    }

    private function compareColumnNames(string $name1, string $name2): int
    {
        if ($name1 === 'id') return -1;
        if ($name2 === 'id') return 1;
        return strcmp($name1, $name2);
    }

    /**
     * @param ValueSet $valueSet1
     * @param ValueSet $valueSet2
     * @return int
     */
    private function compareValueSets(array $valueSet1, array $valueSet2): int
    {
        foreach ($valueSet1 as $column => $value1) {
            $value2 = $valueSet2[$column];
            $compare = $this->compareValues($value1, $value2);
            if ($compare !== 0) return $compare;
        }
        return 0;
    }

    /**
     * @param scalar $value1
     * @param scalar $value2
     * @return int
     */
    private function compareValues(float|bool|int|string|null $value1, float|bool|int|string|null $value2): int
    {
        if (is_numeric($value1) && is_numeric($value2)) {
            return match (true) {
                $value1 > $value2 => 1,
                $value1 < $value2 => -1,
                default => 0
            };
        }
        return strcmp((string)$value1, (string)$value2);
    }
}