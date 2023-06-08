<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Tests\Unit\Classes\Dataset\Attribute;

use HJerichen\DBUnit\Dataset\Attribute\DatasetForExpected;
use HJerichen\DBUnit\Dataset\DatasetArray;
use PHPUnit\Framework\TestCase;

class DatasetForExpectedTest extends TestCase
{
    public function test_getDataset(): void
    {
        $data = ['user' => [['id' => 1]]];
        $attribute = new DatasetForExpected($data);

        $expected = new DatasetArray($data);
        $actual = $attribute->getDataset();
        $this->assertEquals($expected, $actual);
    }
}
