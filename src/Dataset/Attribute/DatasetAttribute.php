<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Dataset\Attribute;

use HJerichen\DBUnit\Dataset\Dataset;
use HJerichen\DBUnit\Dataset\DatasetArray;

/**
 * @psalm-import-type DatasetArrayData from DatasetArray
 */
abstract class DatasetAttribute
{
    /**
     * @param DatasetArrayData $dataset
     * @noinspection PhpDocSignatureInspection
     */
    public function __construct(
        private readonly array $dataset,
    ) {
    }

    public function getDataset(): Dataset
    {
        return new DatasetArray($this->dataset);
    }
}
