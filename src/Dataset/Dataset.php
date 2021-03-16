<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Dataset;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
interface Dataset
{
    /** @return Table[] */
    public function getTables(): array;
}