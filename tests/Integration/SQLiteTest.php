<?php
declare(strict_types=1);

namespace HJerichen\DBUnit\Tests\Integration;

use HJerichen\DBUnit\Dataset\Attribute\DatasetForExpected;
use HJerichen\DBUnit\Dataset\Attribute\DatasetForSetup;
use HJerichen\DBUnit\Dataset\Dataset;
use HJerichen\DBUnit\Dataset\DatasetArray;
use HJerichen\DBUnit\Dataset\DatasetComposite;
use HJerichen\DBUnit\SQLiteTestCaseTrait;
use PDO;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class SQLiteTest extends TestCase
{
    use SQLiteTestCaseTrait {
        assertDatasetEqualsCurrent as private assertDatasetEqualsCurrentDataset;
    }

    private PDO $database;

    protected function getDatabase(): PDO
    {
        if (!isset($this->database)) {
            $this->database = new PDO('sqlite::memory:');
            $this->createTables();
        }
        return $this->database;
    }

    /** @psalm-suppress InternalMethod */
    protected function getDatasetForSetup(): Dataset
    {
        if ($this->getName() === 'testWithMultipleDatasets') {
            return new DatasetComposite([
                new DatasetArray([
                    'product' => [
                        ['id' => 1, 'ean' => '123', 'stock' => 0],
                    ]
                ]),
                new DatasetArray([
                    'product' => [
                        ['id' => 2, 'ean' => '456', 'stock' => 10],
                    ]
                ]),
            ]);
        }

        return $this->getDatasetForSetupFromAttribute() ?? new DatasetArray([
            'product' => [
                ['id' => 1, 'ean' => '123', 'stock' => 0],
                ['id' => 2, 'ean' => '456', 'stock' => 10],
            ]
        ]);
    }

    /* TESTS */

    public function testImportAndCompare(): void
    {
        $expected = new DatasetArray([
            'product' => [
                ['id' => 1, 'ean' => '123', 'stock' => 0],
                ['id' => 2, 'ean' => '456', 'stock' => 10],
            ]
        ]);

        $this->expectNoException();
        $this->assertDatasetEqualsCurrent($expected);
    }

    public function testImportAndCompareReducedFields(): void
    {
        $expected = new DatasetArray([
            'product' => [
                ['id' => 1, 'ean' => '123'],
                ['id' => 2, 'ean' => '456'],
            ]
        ]);

        $this->expectNoException();
        $this->assertDatasetEqualsCurrent($expected);
    }

    #[DatasetForSetup([
        'product' => [
            ['id' => 1, 'ean' => '123', 'stock' => 0],
        ],
        'productExtension' => [
            ['id' => 1, 'productId' => 1],
        ]
    ])]
    #[DatasetForExpected([
        'product' => [
            ['id' => 1, 'ean' => '123', 'stock' => 0],
        ],
        'productExtension' => [
            ['id' => 1, 'productId' => 1],
        ]
    ])]
    public function testWithForeignKeys(): void
    {
        $this->expectNoException();
        $this->assertExpectedDatasetFromAttributeEqualsCurrent();
    }

    public function testWithMultipleDatasets(): void
    {
        $expected = new DatasetComposite([
            new DatasetArray([
                'product' => [
                    ['id' => 1, 'ean' => '123', 'stock' => 0],
                ]
            ]),
            new DatasetArray([
                'product' => [
                    ['id' => 2, 'ean' => '456', 'stock' => 10],
                ]
            ]),
        ]);
        $this->expectNoException();
        $this->assertDatasetEqualsCurrent($expected);
    }

    /* HELPERS */

    private function createTables(): void
    {
        $sql = file_get_contents(__DIR__ . '/SQLiteTestTables.sqlite');
        $this->database->exec($sql);
    }

    private function expectNoException(): void
    {
        self::assertEquals(1, 1);
    }

    protected function assertExpectedDatasetFromAttributeEqualsCurrent(): void
    {
        $expected = $this->getDatasetForExpectedFromAttribute();
        if (!$expected) throw new RuntimeException('No expected dataset provided via attribute.');

        $this->assertDatasetEqualsCurrent($expected);
    }

    /**
     * @psalm-suppress InternalMethod
     * @psalm-suppress InternalClass
     */
    protected function assertDatasetEqualsCurrent(Dataset $expected): void
    {
        try {
            $this->assertDatasetEqualsCurrentDataset($expected);
        } catch (ComparisonFailure $failure) {
            throw new ExpectationFailedException($failure->getMessage(), $failure);
        }
    }
}