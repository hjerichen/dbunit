<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Dataset;

class DatasetComposite implements Dataset
{
    /** @param $datasets list<Dataset> */
    public function __construct(
        private readonly array $datasets
    ) {
    }

    /**
     * @return list<Table>
     * @noinspection PhpRedundantVariableDocTypeInspection
     */
    public function getTables(): array
    {
        /** @var Table[] $tables */
        $tables = [];

        /** @var Dataset $dataset */
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
