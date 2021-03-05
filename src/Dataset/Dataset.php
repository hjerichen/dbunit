<?php

namespace HJerichen\DBUnit\Dataset;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
interface Dataset
{
    /** @return Table[] */
    public function getTables(): array;
}