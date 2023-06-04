<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Comparator;

use HJerichen\DBUnit\Dataset\Dataset;
use HJerichen\DBUnit\Dataset\TableSorter;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class DatasetToArrayConverter
{
    public function __construct(
        private readonly TableSorter $tableSorter
    ) {
    }

    public function convertDatasetToArray(Dataset $dataset): array
    {
        $array = [];
        foreach ($dataset->getTables() as $table) {
            $this->tableSorter->sortTable($table);
            $valueSets = $table->getValueSets();
            asort($valueSets);
            $array[$table->getName()] = $valueSets;
        }
        asort($array);
        return $array;
    }
}