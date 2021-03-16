<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Dataset;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class DatasetArray implements Dataset
{
    /** @var Table[] */
    private array $tables = [];

    public function __construct(array $data)
    {
        $this->buildTables($data);
    }

    private function buildTables(array $data): void
    {
        foreach ($data as $tableName => $valueSets) {
            $this->tables[] = new Table($tableName, $valueSets ?? []);
        }
    }

    public function getTables(): array
    {
        return $this->tables;
    }
}