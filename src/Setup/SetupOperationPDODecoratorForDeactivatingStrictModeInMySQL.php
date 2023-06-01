<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Setup;

use HJerichen\DBUnit\Dataset\Dataset;
use PDO;

class SetupOperationPDODecoratorForDeactivatingStrictModeInMySQL implements SetupOperation
{
    private SetupOperation $setupOperation;
    private PDO $database;

    private Dataset $dataset;
    private string $currentSQLMode;

    public function __construct(
        SetupOperation $setupOperation,
        PDO $database
    ) {
        $this->setupOperation = $setupOperation;
        $this->database = $database;
    }

    public function execute(Dataset $dataset): void
    {
        try {
            $this->dataset = $dataset;
            $this->fetchCurrentSQLMode();
            $this->deactivateStrictMode();
            $this->executeSetUpOperation();
        } finally {
            $this->restoreSQLMode();
            $this->cleanup();
        }
    }

    private function fetchCurrentSQLMode(): void
    {
        $this->currentSQLMode = trim($this->database->query("SELECT @@sql_mode")->fetchColumn());
    }

    private function deactivateStrictMode(): void
    {
        if ($this->currentSQLMode === '') return;
        $this->database->exec("SET SESSION sql_mode=''");
    }

    private function executeSetUpOperation(): void
    {
        $this->setupOperation->execute($this->dataset);
    }

    private function restoreSQLMode(): void
    {
        if (!isset($this->currentSQLMode) || $this->currentSQLMode === '') return;
        $sqlMode = $this->database->quote($this->currentSQLMode);
        $this->database->exec("SET SESSION sql_mode='$sqlMode'");
    }

    private function cleanup(): void
    {
        unset(
            $this->currentSQLMode,
            $this->dataset,
        );
    }
}
