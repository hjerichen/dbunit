<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Dataset;

class DatasetComposite implements Dataset
{
    /** @var list<Dataset> */
    private array $datasets;

    /** @param $datasets list<Dataset> */
    public function __construct(array $datasets)
    {
        $this->datasets = $datasets;
    }

    /** @return Table[] */
    public function getTables(): array
    {
        /** @var Table[] $tables */
        $tables = [];
        foreach ($this->datasets as $dataset) {
            foreach ($dataset->getTables() as $table) {
                if (!isset($tables[$table->getName()])) {
                    $tables[$table->getName()] = $table;
                } else {
                    $valueSets = [...$tables[$table->getName()]->getValueSets(), ...$table->getValueSets()];
                    $tables[$table->getName()] = new Table($table->getName(), $valueSets);
                }
            }
        }
        return array_values($tables);
    }
}
