<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Tests\Unit\Dataset;

use HJerichen\DBUnit\Dataset\Table;
use HJerichen\DBUnit\Dataset\TableSorter;
use PHPUnit\Framework\TestCase;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class TableSorterTest extends TestCase
{
    private TableSorter $tableSorter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tableSorter = new TableSorter();
    }

    /* TESTS */

    public function testSortColumnsByName(): void
    {
        $table = new Table('test', [
            ['bb' => 1, 'aa' => 2]
        ]);
        $this->tableSorter->sortTable($table);

        $expected = [
            ['aa' => 2, 'bb' => 1]
        ];
        self::assertSame($expected, $table->getValueSets());
    }

    public function testSortColumnsWithIdFirst(): void
    {
        $table = new Table('test', [
            ['bb' => 1, 'aa' => 2, 'id' => 3]
        ]);
        $this->tableSorter->sortTable($table);

        $expected = [
            ['id' => 3, 'aa' => 2, 'bb' => 1]
        ];
        self::assertSame($expected, $table->getValueSets());
    }

    public function testSortValueSets(): void
    {
        $table = new Table('test', [
            ['bb' => 1, 'aa' => 3, 'id' => 3],
            ['bb' => 2, 'aa' => 2, 'id' => 1],
            ['bb' => 3, 'aa' => 1, 'id' => 2],
        ]);
        $this->tableSorter->sortTable($table);

        $expected = [
            ['id' => 1, 'aa' => 2, 'bb' => 2],
            ['id' => 2, 'aa' => 1, 'bb' => 3],
            ['id' => 3, 'aa' => 3, 'bb' => 1],
        ];
        self::assertSame($expected, $table->getValueSets());
    }

    public function testSortValueSetsWithMismatchingColumns(): void
    {
        $table = new Table('test', [
            ['bb' => 1, 'aa' => 3],
            ['aa' => 2, 'id' => 1],
            ['bb' => 3, 'aa' => 1, 'id' => 2],
        ]);
        $this->tableSorter->sortTable($table);

        $expected = [
            ['id' => null, 'aa' => 3, 'bb' => 1],
            ['id' => 1, 'aa' => 2, 'bb' => null],
            ['id' => 2, 'aa' => 1, 'bb' => 3],
        ];
        self::assertSame($expected, $table->getValueSets());
    }
}