<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Tests\Unit\StrictMode;

use HJerichen\DBUnit\StrictMode\StrictModeHandlerMySQL;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class StrictModeHandlerMySQLTest extends TestCase
{
    use ProphecyTrait;

    private StrictModeHandlerMySQL $strictModeHandler;
    /** @var ObjectProphecy<PDO> */
    private ObjectProphecy $database;

    private string $sqlMode;

    protected function setUp(): void
    {
        parent::setUp();
        $this->database = $this->prophesize(PDO::class);

        $this->setUpDefaultBehaviourOfProphecies();

        $this->strictModeHandler = new StrictModeHandlerMySQL($this->database->reveal());
    }

    /* TESTS */

    public function test_disableStrictMode(): void
    {
        $this->setUpCurrentSQLMode('strict');

        $this->expectRemoveSQLModeWillBeCalled();

        $this->strictModeHandler->disableStrictMode();
    }

    public function test_disableStrictMode_withEmptySqlMode(): void
    {
        $this->setUpCurrentSQLMode('');

        $this->expectRemoveSQLModeWillNotBeCalled();

        $this->strictModeHandler->disableStrictMode();
    }

    public function test_restoreStrictMode(): void
    {
        $this->setUpCurrentSQLMode('strict');

        $this->expectRemoveSQLModeWillBeCalled();
        $this->expectRestoreSQLModeWillBeCalled();

        $this->strictModeHandler->disableStrictMode();
        $this->strictModeHandler->restoreStrictMode();
    }

    public function test_restoreStrictMode_withEmptySqlMode(): void
    {
        $this->setUpCurrentSQLMode('');

        $this->expectRemoveSQLModeWillNotBeCalled();
        $this->expectRestoreSQLModeWillNotBeCalled();

        $this->strictModeHandler->disableStrictMode();
        $this->strictModeHandler->restoreStrictMode();
    }

    public function test_restoreStrictMode_withDisableStrictModeNotCalledBefore(): void
    {
        $this->setUpCurrentSQLMode('strict');

        $this->expectRemoveSQLModeWillNotBeCalled();
        $this->expectRestoreSQLModeWillNotBeCalled();

        $this->strictModeHandler->restoreStrictMode();
    }

    /* HELPERS */

    private function setUpDefaultBehaviourOfProphecies(): void
    {
        $this->database
            ->quote(Argument::any())
            ->will(static fn(array $parameters): string => "'$parameters[0]'");
    }

    private function setUpCurrentSQLMode(string $sqlMode): void
    {
        $this->sqlMode = $sqlMode;

        $statement = $this->prophesize(PDOStatement::class);
        $statement->fetchColumn()->willReturn($sqlMode);

        $this->database->query("SELECT @@sql_mode")->willReturn($statement);
    }

    private function expectRemoveSQLModeWillBeCalled(): void
    {
        $this->database->exec("SET SESSION sql_mode=''")->shouldBeCalledOnce();
    }

    private function expectRemoveSQLModeWillNotBeCalled(): void
    {
        $this->database->exec("SET SESSION sql_mode=''")->shouldNotBeCalled();
    }

    private function expectRestoreSQLModeWillBeCalled(): void
    {
        $this->database->exec("SET SESSION sql_mode='$this->sqlMode'")->shouldBeCalledOnce();
    }

    private function expectRestoreSQLModeWillNotBeCalled(): void
    {
        $this->database->exec("SET SESSION sql_mode='$this->sqlMode'")->shouldNotBeCalled();
    }
}
