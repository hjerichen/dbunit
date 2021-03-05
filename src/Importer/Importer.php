<?php

namespace HJerichen\DBUnit\Importer;

use HJerichen\DBUnit\Dataset\Dataset;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
interface Importer
{
    public function import(Dataset $dataset): void;
}