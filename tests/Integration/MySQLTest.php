<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Tests\Integration;

use HJerichen\DBUnit\Dataset\Dataset;
use HJerichen\DBUnit\Dataset\DatasetArray;
use HJerichen\DBUnit\Dataset\DatasetComposite;
use HJerichen\DBUnit\MySQLTestCaseTrait;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class MySQLTest extends TestCase
{
    use MySQLTestCaseTrait;

    private PDO $database;

    /**
     * @noinspection PhpUndefinedConstantInspection
     * @psalm-suppress UndefinedConstant
     * @psalm-suppress MixedArgument
     */
    protected function getDatabase(): PDO
    {
        if (!isset($this->database)) {
            $this->database = new PDO(MYSQL_DSN, MYSQL_USER, MYSQL_PASS);
            $this->database->exec("SET SESSION sql_mode='STRICT_TRANS_TABLES'");
            $this->createTables();
        }
        return $this->database;
    }

    /** @psalm-suppress InternalMethod */
    protected function getDatasetForSetup(): Dataset
    {
        if ($this->getName() === 'testWithForeignKeys') {
            return new DatasetArray([
                'product' => [
                    ['id' => 1, 'ean' => '123', 'stock' => 0],
                ],
                'productExtension' => [
                    ['id' => 1, 'productId' => 1]
                ]
            ]);
        }
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
        return new DatasetArray([
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

    public function testWithForeignKeys(): void
    {
        $expected = new DatasetArray([
            'product' => [
                ['id' => 1, 'ean' => '123', 'stock' => 0],
            ],
            'productExtension' => [
                ['id' => 1, 'productId' => 1]
            ]
        ]);

        $this->expectNoException();
        $this->assertDatasetEqualsCurrent($expected);
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
        $sql = file_get_contents(__DIR__ . '/MySQLTestTables.sql');
        $this->database->exec($sql);
    }

    private function expectNoException(): void
    {
        self::assertEquals(1, 1);
    }
}