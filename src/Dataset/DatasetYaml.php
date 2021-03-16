<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Dataset;

use Symfony\Component\Yaml\Yaml;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class DatasetYaml extends DatasetArray
{
    public function __construct(string $yamlFile)
    {
        $data = $this->getData($yamlFile);
        parent::__construct($data);
    }

    private function getData(string $yamlFile): array
    {
        $tables = Yaml::parseFile($yamlFile);
        return $tables ?? [];
    }
}