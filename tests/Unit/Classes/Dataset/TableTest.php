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

    public function testGetColumns(): void
    {
        $name = 'table';
        $table = new Table($name, [
            ['id' => 1, 'name' => 'name-1'],
            ['id' => 2, 'text' => 'text-1'],
        ]);

        $expected = ['id', 'name', 'text'];
        $actual = $table->getColumns();
        self::assertEquals($expected, $actual);
    }
}
