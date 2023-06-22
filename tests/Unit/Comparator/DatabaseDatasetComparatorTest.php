<?php
/** @noinspection PhpVoidFunctionResultUsedInspection */
declare(strict_types=1);

namespace HJerichen\DBUnit\Tests\Unit\Comparator;

use HJerichen\DBUnit\Comparator\DatabaseDatasetComparator;
use HJerichen\DBUnit\Dataset\Database\DatabaseDataset;
use HJerichen\DBUnit\Dataset\DatasetArray;
use HJerichen\DBUnit\Dataset\Table;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class DatabaseDatasetComparatorTest extends TestCase
{
    use ProphecyTrait;

    private DatabaseDatasetComparator $comparator;
    /** @var ObjectProphecy<DatabaseDataset>  */
    private ObjectProphecy $databaseDataset;

    protected function setUp(): void
    {
        parent::setUp();
        $this->databaseDataset = $this->prophesize(DatabaseDataset::class);

        $this->comparator = new DatabaseDatasetComparator();
    }

    /* TESTS */

    public function testForEqualDataset(): void
    {
        $expected = new DatasetArray([
            'some-table' => [
                ['id' => 1, 'name' => 'test-1'],
                ['id' => 2, 'name' => 'test-2'],
            ]
        ]);

        $table = new Table('some-table', [
            ['id' => 2, 'name' => 'test-2'],
            ['id' => 1, 'name' => 'test-1'],
        ]);
        $this->databaseDataset
            ->setTableColumns('some-table', ['id', 'name'])
            ->shouldBeCalledOnce();
        $this->databaseDataset
            ->getTables()
            ->willReturn([$table]);

        $this->expectNoException();
        $this->comparator->assertEquals($expected, $this->databaseDataset->reveal());
    }

    public function testForEqualDatasetWithoutId(): void
    {
        $expected = new DatasetArray([
            'some-table' => [
                ['name' => 'aaa', 'text' => 'aaa'],
                ['name' => 'bbb', 'text' => 'bbb'],
                ['name' => 'bbb', 'text' => 'ccc'],
                ['name' => 'bbb', 'text' => 'ccc'],
            ]
        ]);

        $table = new Table('some-table', [
            ['name' => 'bbb', 'text' => 'ccc'],
            ['name' => 'aaa', 'text' => 'aaa'],
            ['name' => 'bbb', 'text' => 'ccc'],
            ['name' => 'bbb', 'text' => 'bbb'],
        ]);
        $this->databaseDataset
            ->setTableColumns('some-table', ['name', 'text'])
            ->shouldBeCalledOnce();
        $this->databaseDataset
            ->getTables()
            ->willReturn([$table]);

        $this->expectNoException();
        $this->comparator->assertEquals($expected, $this->databaseDataset->reveal());
    }

    public function testForEqualDatasetButTablesNotSorted(): void
    {
        $expected = new DatasetArray([
            'aaa' => [
                ['id' => 1, 'name' => 'test-1'],
            ],
            'bbb' => [
                ['id' => 2, 'text' => 'test-2'],
            ],
        ]);

        $table1 = new Table('bbb', [
            ['id' => 2, 'text' => 'test-2'],
        ]);
        $table2 = new Table('aaa', [
            ['id' => 1, 'name' => 'test-1'],
        ]);
        $this->databaseDataset
            ->setTableColumns('aaa', ['id', 'name'])
            ->shouldBeCalledOnce();
        $this->databaseDataset
            ->setTableColumns('bbb', ['id', 'text'])
            ->shouldBeCalledOnce();
        $this->databaseDataset->getTables()->willReturn([$table1, $table2]);

        $this->expectNoException();
        $this->comparator->assertEquals($expected, $this->databaseDataset->reveal());
    }

    public function testForNotEqualDataset(): void
    {
        $expected = new DatasetArray([
            'some-table' => [
                ['id' => 1, 'name' => 'test-1'],
                ['id' => 2, 'name' => 'test-2'],
            ]
        ]);

        $table = new Table('some-table', [
            ['id' => 1, 'name' => 'test-1'],
            ['id' => 2, 'name' => 'test-1'],
        ]);
        $this->databaseDataset
            ->setTableColumns('some-table', ['id', 'name'])
            ->shouldBeCalledOnce();
        $this->databaseDataset
            ->getTables()
            ->willReturn([$table]);

        $this->expectException(ComparisonFailure::class);
        $this->comparator->assertEquals($expected, $this->databaseDataset->reveal());
    }

    /* HELPERS */

    private function expectNoException(): void
    {
        self::assertTrue(true);
    }
}