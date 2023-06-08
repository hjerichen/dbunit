<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Dataset;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 * @psalm-import-type ValueSet from Table
 * @psalm-type DatasetArrayData = array<string,list<ValueSet>|null>
 */
class DatasetArray implements Dataset
{
    /** @var list<Table> */
    private array $tables = [];

    /** @param DatasetArrayData $data */
    public function __construct(array $data)
    {
        $this->buildTables($data);
    }

    /** @param DatasetArrayData $data */
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