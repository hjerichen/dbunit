<?php
/** @noinspection PhpVoidFunctionResultUsedInspection */
declare(strict_types=1);

namespace HJerichen\DBUnit\Tests\Unit\Classes\Setup;

use HJerichen\DBUnit\Dataset\DatasetArray;
use HJerichen\DBUnit\Setup\SetupOperation;
use HJerichen\DBUnit\Setup\SetupOperationPDODecoratorForDeactivatingStrictModeInMySQL;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class SetupOperationPDODecoratorForDeactivatingStrictModeInMySQLTest extends TestCase
{
    use ProphecyTrait;

    private SetupOperationPDODecoratorForDeactivatingStrictModeInMySQL $decorator;
    /** @var ObjectProphecy<SetupOperation> */
    private ObjectProphecy $setUpOperation;
    /** @var ObjectProphecy<PDO> */
    private ObjectProphecy $database;

    private DatasetArray $dataset;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpOperation = $this->prophesize(SetupOperation::class);
        $this->database = $this->prophesize(PDO::class);
        $this->database->quote(Argument::any())->will(fn(array $parameters): string => "'$parameters[0]'");

        $this->dataset = new DatasetArray(['user' => ['id' => 1]]);

        $this->decorator = $this->buildNewInstanceForTest();
    }

    /* TESTS */

    public function test_execute_forNoSQLMode(): void
    {
        $this->setUpSqlMode('');

        $this->expectCallingSetUpOperation();

        $this->decorator->execute($this->dataset);
    }

    public function test_execute_forSetSQLMode(): void
    {
        $this->setUpSqlMode("SomeMode");

        $this->expectRemovingSqlMode();
        $this->expectCallingSetUpOperation();
        $this->expectRestoringSqlMode("'SomeMode'");

        $this->decorator->execute($this->dataset);
    }

    public function test_cleanup(): void
    {
        $this->test_execute_forSetSQLMode();

        $expected = $this->buildNewInstanceForTest();
        $actual = $this->decorator;
        $this->assertEquals($expected, $actual);
    }

    /* HELPERS */

    private function buildNewInstanceForTest(): SetupOperationPDODecoratorForDeactivatingStrictModeInMySQL
    {
        return new SetupOperationPDODecoratorForDeactivatingStrictModeInMySQL(
            $this->setUpOperation->reveal(),
            $this->database->reveal(),
        );
    }

    private function setUpSqlMode(string $sqlMode): void
    {
        $statement = $this->prophesize(PDOStatement::class);
        $statement->fetchColumn()->willReturn($sqlMode);
        $this->database->query("SELECT @@sql_mode")->willReturn($statement);
    }

    private function expectCallingSetUpOperation(): void
    {
        $this->setUpOperation->execute($this->dataset)->shouldBeCalledOnce();
    }

    private function expectRemovingSqlMode(): void
    {
        $this->database->exec("SET SESSION sql_mode=''")->shouldBeCalledOnce();
    }

    private function expectRestoringSqlMode(string $sqlMode): void
    {
        $this->database->exec("SET SESSION sql_mode=$sqlMode")->shouldBeCalledOnce();
    }
}
