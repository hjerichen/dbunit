<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Tests\Unit\ForeignKey;

use HJerichen\DBUnit\ForeignKey\ForeignKeyHandlerMySQL;
use PDO;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class ForeignKeyHandlerMySQLTest extends TestCase
{
    use ProphecyTrait;

    private ForeignKeyHandlerMySQL $foreignKeyHandler;
    /** @var ObjectProphecy<PDO> */
    private ObjectProphecy $database;

    protected function setUp(): void
    {
        parent::setUp();
        $this->database = $this->prophesize(PDO::class);

        $this->foreignKeyHandler = new ForeignKeyHandlerMySQL($this->database->reveal());
    }

    /* TESTS */

    public function test_disableForeignKeyCheck(): void
    {
        $this->database
            ->exec('SET foreign_key_checks=0')
            ->shouldBeCalledOnce();

        $this->foreignKeyHandler->disableForeignKeyCheck();
    }

    public function test_enableForeignKeyCheck(): void
    {
        $this->database
            ->exec('SET foreign_key_checks=1')
            ->shouldBeCalledOnce();

        $this->foreignKeyHandler->enableForeignKeyCheck();
    }
}
