<?php
/** @noinspection PhpVoidFunctionResultUsedInspection */
declare(strict_types=1);

namespace HJerichen\DBUnit\Tests\Unit\Setup;

use Exception;
use HJerichen\DBUnit\DatabaseCleanup\DatabaseCleaner;
use HJerichen\DBUnit\Dataset\Dataset;
use HJerichen\DBUnit\Dataset\DatasetArray;
use HJerichen\DBUnit\ForeignKey\ForeignKeyHandler;
use HJerichen\DBUnit\Importer\Importer;
use HJerichen\DBUnit\Setup\SetupOperationConstruct;
use HJerichen\DBUnit\StrictMode\StrictModeHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class SetupOperationConstructTest extends TestCase
{
    use ProphecyTrait;

    private SetupOperationConstruct $setupOperation;
    /** @var ObjectProphecy<StrictModeHandler>  */
    private ObjectProphecy $strictModeHandler;
    /** @var ObjectProphecy<ForeignKeyHandler>  */
    private ObjectProphecy $foreignKeyHandler;
    /** @var ObjectProphecy<DatabaseCleaner>  */
    private ObjectProphecy $databaseCleaner;
    /** @var ObjectProphecy<Importer>  */
    private ObjectProphecy $importer;

    private Dataset $dataset;

    protected function setUp(): void
    {
        parent::setUp();
        $this->strictModeHandler = $this->prophesize(StrictModeHandler::class);
        $this->foreignKeyHandler = $this->prophesize(ForeignKeyHandler::class);
        $this->databaseCleaner = $this->prophesize(DatabaseCleaner::class);
        $this->importer = $this->prophesize(Importer::class);

        $this->dataset = new DatasetArray([]);

        $this->setupOperation = new SetupOperationConstruct(
            $this->strictModeHandler->reveal(),
            $this->foreignKeyHandler->reveal(),
            $this->databaseCleaner->reveal(),
            $this->importer->reveal(),
        );
    }

    /* TESTS */

    public function test_execute(): void
    {
        $this->expectDisableStrictModeWillBeCalled();
        $this->expectDisableForeignKeyChecksWillBeCalled();
        $this->expectCleanupDatabaseWillBeCalled();
        $this->expectImportDatasetWillBeCalled();
        $this->expectEnableForeignKeyChecksWillBeCalled();
        $this->expectRestoreStrictModeWillBeCalled();

        $this->setupOperation->execute($this->dataset);
    }

    public function test_execute_forImportThrowsException(): void
    {
        $exception = new Exception;
        $this->setupImportThrowsException($exception);

        $this->expectDisableStrictModeWillBeCalled();
        $this->expectDisableForeignKeyChecksWillBeCalled();
        $this->expectCleanupDatabaseWillBeCalled();
        $this->expectEnableForeignKeyChecksWillBeCalled();
        $this->expectRestoreStrictModeWillBeCalled();
        $this->expectExceptionObject($exception);

        $this->setupOperation->execute($this->dataset);
    }

    /* HELPERS */

    private function setupImportThrowsException(Exception $exception): void
    {
        $this->importer->import(Argument::any())->willThrow($exception);
    }

    private function expectDisableStrictModeWillBeCalled(): void
    {
        $this->strictModeHandler->disableStrictMode()->shouldBeCalledOnce();
    }

    private function expectRestoreStrictModeWillBeCalled(): void
    {
        $this->strictModeHandler->restoreStrictMode()->shouldBeCalledOnce();
    }

    private function expectDisableForeignKeyChecksWillBeCalled(): void
    {
        $this->foreignKeyHandler->disableForeignKeyCheck()->shouldBeCalledOnce();
    }

    private function expectEnableForeignKeyChecksWillBeCalled(): void
    {
        $this->foreignKeyHandler->enableForeignKeyCheck()->shouldBeCalledOnce();
    }

    private function expectCleanupDatabaseWillBeCalled(): void
    {
        $this->databaseCleaner->execute()->shouldBeCalledOnce();
    }

    private function expectImportDatasetWillBeCalled(): void
    {
        $this->importer->import($this->dataset)->shouldBeCalledOnce();
    }
}