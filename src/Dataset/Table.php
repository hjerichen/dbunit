<?php

namespace HJerichen\DBUnit\Dataset;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class Table
{
    private string $name;
    private array $valueSets;

    public function __construct(string $name, array $valueSets)
    {
        $this->name = $name;
        $this->valueSets = $valueSets;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColumns(): array
    {
        if (count($this->valueSets) === 0) {
            return [];
        }
        return array_keys(reset($this->valueSets));
    }

    public function getValueSets(): array
    {
        return $this->valueSets;
    }
}