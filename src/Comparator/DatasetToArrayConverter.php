<?php

namespace HJerichen\DBUnit\Comparator;

use HJerichen\DBUnit\Dataset\Dataset;
use HJerichen\DBUnit\Dataset\TableSorter;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class DatasetToArrayConverter
{
    private TableSorter $tableSorter;

    public function __construct(TableSorter $tableSorter)
    {
        $this->tableSorter = $tableSorter;
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