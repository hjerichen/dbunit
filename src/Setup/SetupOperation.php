<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Setup;

use HJerichen\DBUnit\Dataset\Dataset;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
interface SetupOperation
{
    public function execute(Dataset $dataset): void;
}