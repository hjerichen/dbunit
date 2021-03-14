<?php

namespace HJerichen\DBUnit\Tests\Unit\Classes\Comparator;

use HJerichen\DBUnit\Comparator\DatasetComparator;
use HJerichen\DBUnit\Dataset\DatasetArray;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class DatasetComparatorTest extends TestCase
{
    private DatasetComparator $comparator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->comparator = new DatasetComparator();
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
        $actual = new DatasetArray([
            'some-table' => [
                ['id' => 2, 'name' => 'test-2'],
                ['id' => 1, 'name' => 'test-1'],
            ]
        ]);

        $this->expectNoException();
        $this->comparator->assertEquals($expected, $actual);
    }

    public function testForEqualDatasetButTablesNotSorted(): void
    {
        $expected = new DatasetArray([
            'aaa' => [
                ['id' => 1, 'name' => 'test-1'],
            ],
            'bbb' => [
                ['id' => 2, 'name' => 'test-2'],
            ],
        ]);
        $actual = new DatasetArray([
            'bbb' => [
                ['id' => 2, 'name' => 'test-2'],
            ],
            'aaa' => [
                ['id' => 1, 'name' => 'test-1'],
            ],
        ]);

        $this->expectNoException();
        $this->comparator->assertEquals($expected, $actual);
    }

    public function testForNotEqualDataset(): void
    {
        $expected = new DatasetArray([
            'some-table' => [
                ['id' => 1, 'name' => 'test-1'],
                ['id' => 2, 'name' => 'test-2'],
            ]
        ]);
        $actual = new DatasetArray([
            'some-table' => [
                ['id' => 1, 'name' => 'test-1'],
                ['id' => 2, 'name' => 'test-1'],
            ]
        ]);

        $this->expectException(ComparisonFailure::class);
        $this->comparator->assertEquals($expected, $actual);
    }

    /* HELPERS */

    private function expectNoException(): void
    {
        self::assertTrue(true);
    }
}