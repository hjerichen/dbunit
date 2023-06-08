<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Tests\Unit;

use HJerichen\DBUnit\Importer\ImporterPDO;
use HJerichen\DBUnit\Dataset\DatasetYaml;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ImportYamlTest extends TestCase
{
    use ProphecyTrait;

    private ImporterPDO $importer;
    /** @var ObjectProphecy<PDO>  */
    private ObjectProphecy $database;

    protected function setUp(): void
    {
        parent::setUp();
        $this->database = $this->prophesize(PDO::class);

        $this->importer = new ImporterPDO($this->database->reveal());
    }

    /* TESTS */

    public function testForEmptyFile(): void
    {
        $this->expectNoImport();
        $this->importer->import($this->getDataset());
    }

    public function testForEmptyTable(): void
    {
        $this->expectNoImport();
        $this->importer->import($this->getDataset());
    }

    public function testForOneTableOneColumn(): void
    {
        $this->expectImport([
            [
                [
                    ':id_0' => 2
                ]
            ]
        ]);
        $this->importer->import($this->getDataset());
    }

    public function testForOneTableMultipleColumns(): void
    {
        $this->expectImport([
            [
                [
                    ':id_0' => 2,
                    ':name_0' => 'jon doe',
                    ':description_0' => null,
                    ':comment_0' => 'my "test"',
                ]
            ]
        ]);
        $this->importer->import($this->getDataset());
    }

    public function testForOneTableMultipleValueSets(): void
    {
        $this->expectImport([
            [
                [
                    ':id_0' => 2,
                    ':name_0' => 'jon doe',
                    ':description_0' => null,
                    ':comment_0' => 'my "test"',
                ],
                [
                    ':id_1' => 4,
                    ':name_1' => 'jane doe',
                    ':description_1' => 'test',
                    ':comment_1' => 'comment',
                ],
                [
                    ':id_2' => 5,
                    ':name_2' => 'jane dome',
                    ':description_2' => 'test 5',
                    ':comment_2' => null,
                ],
            ]
        ]);
        $this->importer->import($this->getDataset());
    }

    public function testForMultipleTables(): void
    {
        $this->expectImport([
            [
                [
                    ':id_0' => 2,
                    ':name_0' => 'jon doe',
                    ':description_0' => null,
                    ':comment_0' => 'my "test"',
                ]
            ],
            [
                [
                    ':id_0' => 1,
                    ':key_0' => 'key 1',
                    ':value_0' => 'value 1',
                ]
            ],
        ]);
        $this->importer->import($this->getDataset());
    }

    public function testForMissMatchedColumns(): void
    {
        $this->expectImport([
            [
                [
                    ':id_0' => 2,
                    ':name_0' => 'jon doe',
                    ':description_0' => null,
                    ':comment_0' => 'my "test"',
                ],
                [
                    ':id_1' => 4,
                    ':name_1' => 'jane doe',
                    ':description_1' => 'test',
                    ':comment_1' => 'comment',
                ],
            ],
            [
                [
                    ':id_0' => 5,
                    ':name_0' => 'jane dome',
                    ':comment_0' => null,
                ],
            ]
        ]);
        $this->importer->import($this->getDataset());
    }

    /* HELPERS */

    private function expectNoImport(): void
    {
        $this->database->exec(Argument::any())->shouldNotBeCalled();
    }

    /** @param list<list<array<string,mixed>>> $data */
    private function expectImport(array $data): void
    {
        $sqlCommands = file_get_contents($this->getSqlFile());
        $sqlCommands = explode("\n", $sqlCommands);
        for ($i = 0, $max = count($sqlCommands); $i < $max; $i++) {
            $sql = $sqlCommands[$i];
            $values = array_merge(...$data[$i]);
            $statement = $this->prophesize(PDOStatement::class);
            $statement->execute($values)->shouldBeCalledOnce();
            $this->database->prepare($sql)->willReturn($statement->reveal());
        }
    }

    private function getDataset(): DatasetYaml
    {
        return new DatasetYaml($this->getYamlFile());
    }

    /** @psalm-suppress InternalMethod */
    private function getYamlFile(): string
    {
        return __DIR__ . "/../Files/{$this->getName()}.yml";
    }

    /** @psalm-suppress InternalMethod */
    private function getSqlFile(): string
    {
        return __DIR__ . "/../Files/{$this->getName()}.sql";
    }
}