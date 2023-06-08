<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Dataset;

use Symfony\Component\Yaml\Yaml;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 * @psalm-import-type DatasetArrayData from DatasetArray
 */
class DatasetYaml extends DatasetArray
{
    public function __construct(string $yamlFile)
    {
        $data = $this->getData($yamlFile);
        parent::__construct($data);
    }

    /** @return DatasetArrayData */
    private function getData(string $yamlFile): array
    {
        /** @var DatasetArrayData|null $tables */
        $tables = Yaml::parseFile($yamlFile);
        return $tables ?? [];
    }
}