<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Comparator;

use HJerichen\DBUnit\Dataset\Database\DatabaseDataset;
use HJerichen\DBUnit\Dataset\Dataset;
use HJerichen\DBUnit\Dataset\TableSorter;
use SebastianBergmann\Comparator\ArrayComparator;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class DatabaseDatasetComparator
{
    private DatasetToArrayConverter $datasetToArrayConverter;
    private ArrayComparator $comparator;

    public function __construct()
    {
        $this->datasetToArrayConverter = new DatasetToArrayConverter(new TableSorter());
        $this->comparator = new ArrayComparator();
        $this->comparator->setFactory(Factory::getInstance());
    }

    /**
     * @param Dataset $expected
     * @param DatabaseDataset $actual
     *
     * @throws ComparisonFailure
     */
    public function assertEquals(Dataset $expected, DatabaseDataset $actual): void
    {
        foreach ($expected->getTables() as $expectedTable) {
            $actual->setTableColumns($expectedTable->getName(), $expectedTable->getColumns());
        }

        $expectedArray = $this->datasetToArrayConverter->convertDatasetToArray($expected);
        $actualArray = $this->datasetToArrayConverter->convertDatasetToArray($actual);

        $this->comparator->assertEquals($expectedArray, $actualArray);
    }
}