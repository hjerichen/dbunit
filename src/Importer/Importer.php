<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Importer;

use HJerichen\DBUnit\Dataset\Dataset;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
interface Importer
{
    public function import(Dataset $dataset): void;
}