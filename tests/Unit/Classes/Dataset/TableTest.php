<?php

namespace HJerichen\DBUnit\Tests\Unit\Classes\Dataset;

use HJerichen\DBUnit\Dataset\Table;
use PHPUnit\Framework\TestCase;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class TableTest extends TestCase
{
    public function testEmptyTable(): void
    {
        $name = 'table';
        $table = new Table($name, []);

        self::assertSame($name, $table->getName());
        self::assertSame([], $table->getColumns());
        self::assertSame([], $table->getValueSets());
    }
}
