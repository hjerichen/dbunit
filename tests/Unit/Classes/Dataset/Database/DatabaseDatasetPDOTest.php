<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Tests\Unit\Classes\Dataset\Database;

use HJerichen\DBUnit\Dataset\Database\DatabaseDataset;
use HJerichen\DBUnit\Dataset\Database\DatabaseDatasetPDO;
use HJerichen\DBUnit\Dataset\Table;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class DatabaseDatasetPDOTest extends TestCase
{
    use ProphecyTrait;

    private DatabaseDatasetPDO $databaseDataset;
    private ObjectProphecy $database;

    protected function setUp(): void
    {
        parent::setUp();
        $this->database = $this->prophesize(PDO::class);

        $this->databaseDataset = new DatabaseDatasetPDO($this->database->reveal());
    }

    /* TESTS */

    public function testClassImplementsCorrectInterface(): void
    {
        self::assertInstanceOf(DatabaseDataset::class, $this->databaseDataset);
    }

    public function testWithoutSetMetaData(): void
    {
        $expected = [];
        $actual = $this->databaseDataset->getTables();
        self::assertEquals($expected, $actual);
    }

    public function testWithOneTable(): void
    {
        $this->databaseDataset->setTableColumns('someTable', ['id', 'name']);

        $sql = "SELECT `id`, `name` FROM `someTable`";
        $valueSets = [
            ['id' => 1, 'name' => 'name-1'],
            ['id' => 2, 'name' => 'name-2'],
        ];
        $this->setUpDatabase($sql, $valueSets);

        $expected = [new Table('someTable', $valueSets)];
        $actual = $this->databaseDataset->getTables();
        self::assertEquals($expected, $actual);
    }

    public function testWitMultipleTables(): void
    {
        $this->databaseDataset->setTableColumns('someTable1', ['id', 'name']);
        $this->databaseDataset->setTableColumns('someTable2', ['id', 'text']);

        $sql_1 = "SELECT `id`, `name` FROM `someTable1`";
        $valueSets_1 = [
            ['id' => 1, 'name' => 'name-1'],
            ['id' => 2, 'name' => 'name-2'],
        ];
        $this->setUpDatabase($sql_1, $valueSets_1);

        $sql_2 = "SELECT `id`, `text` FROM `someTable2`";
        $valueSets_2 = [
            ['id' => 1, 'text' => 'text-1'],
            ['id' => 2, 'text' => 'text-2'],
        ];
        $this->setUpDatabase($sql_2, $valueSets_2);

        $expected = [
            new Table('someTable1', $valueSets_1),
            new Table('someTable2', $valueSets_2),
        ];
        $actual = $this->databaseDataset->getTables();
        self::assertEquals($expected, $actual);
    }

    /* HELPERS */

    private function setUpDatabase(string $sql, array $valueSets): void
    {
        $statement = $this->prophesize(PDOStatement::class);
        $statement->fetchAll(PDO::FETCH_ASSOC)->willReturn($valueSets);
        $this->database->query($sql)->willReturn($statement->reveal());
    }
}