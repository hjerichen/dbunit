<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Tests\Unit\Classes\Dataset;

use HJerichen\DBUnit\Dataset\DatasetArray;
use HJerichen\DBUnit\Dataset\DatasetComposite;
use PHPUnit\Framework\TestCase;

class DatasetCompositeTest extends TestCase
{
    public function test_getTables(): void
    {
        $dataset = new DatasetComposite([
            new DatasetArray([
                'product' => [
                    ['id' => 1, 'ean' => '123', 'stock' => 0],
                ],
                'test' => [
                    ['id' => 1],
                ],
            ]),
            new DatasetArray([
                'product' => [
                    ['id' => 2, 'ean' => '456', 'stock' => 10],
                ],
                'test2' => [
                    ['id' => 2],
                ],
            ]),
        ]);
        $expected = new DatasetArray([
            'product' => [
                ['id' => 1, 'ean' => '123', 'stock' => 0],
                ['id' => 2, 'ean' => '456', 'stock' => 10],
            ],
            'test' => [
                ['id' => 1],
            ],
            'test2' => [
                ['id' => 2],
            ],
        ]);
        $this->assertEquals($expected->getTables(), $dataset->getTables());
    }

    public function test_getTables_alwaysReturnsSame(): void
    {
        $dataset = new DatasetComposite([
            new DatasetArray([
                'product' => [
                    ['id' => 1, 'ean' => '123', 'stock' => 0],
                ],
            ]),
            new DatasetArray([
                'product' => [
                    ['id' => 2, 'ean' => '456', 'stock' => 10],
                ],
            ]),
        ]);
        $dataset->getTables();

        $expected = new DatasetArray([
            'product' => [
                ['id' => 1, 'ean' => '123', 'stock' => 0],
                ['id' => 2, 'ean' => '456', 'stock' => 10],
            ],
        ]);
        $this->assertEquals($expected->getTables(), $dataset->getTables());
    }
}
