<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Dataset\Database;

use HJerichen\DBUnit\Dataset\Dataset;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
interface DatabaseDataset extends Dataset
{
    /** @param list<string> $columns */
    public function setTableColumns(string $tableName, array $columns): void;
}