<?php
declare(strict_types=1);

namespace HJerichen\DBUnit;

use HJerichen\DBUnit\DatabaseCleanup\DatabaseCleanerMySQL8;
use HJerichen\DBUnit\ForeignKey\ForeignKeyHandlerMySQL;
use HJerichen\DBUnit\Importer\ImporterPDO;
use HJerichen\DBUnit\Setup\SetupOperation;
use HJerichen\DBUnit\Setup\SetupOperationConstruct;
use HJerichen\DBUnit\StrictMode\StrictModeHandlerMySQL;

trait MySQL8TestCaseTrait
{
    use MySQLTestCaseTrait;

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function getSetupOperation(): SetupOperation
    {
        $database = $this->getDatabase();

        return new SetupOperationConstruct(
            new StrictModeHandlerMySQL($database),
            new ForeignKeyHandlerMySQL($database),
            new DatabaseCleanerMySQL8($database),
            new ImporterPDO($database)
        );
    }
}