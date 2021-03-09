<?php

namespace HJerichen\DBUnit\Dataset\Database;

use HJerichen\DBUnit\Dataset\Dataset;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
interface DatabaseDataset extends Dataset
{
    public function setTableColumns(string $tableName, array $columns);
}