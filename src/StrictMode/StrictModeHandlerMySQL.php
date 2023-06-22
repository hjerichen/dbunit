<?php declare(strict_types=1);

namespace HJerichen\DBUnit\StrictMode;

use PDO;

class StrictModeHandlerMySQL implements StrictModeHandler
{
    private string $currentSQLMode;

    public function __construct(
        private readonly PDO $database
    ) {
    }

    public function disableStrictMode(): void
    {
        $this->fetchCurrentSQLMode();
        $this->removeSQLMode();
    }

    public function restoreStrictMode(): void
    {
        if (!isset($this->currentSQLMode)) return;
        $this->restoreSQLMode();
        $this->cleanup();
    }

    private function fetchCurrentSQLMode(): void
    {
        /** @var string $sqlMode */
        $sqlMode = $this->database->query("SELECT @@sql_mode")->fetchColumn();
        $this->currentSQLMode = trim($sqlMode);
    }

    private function removeSQLMode(): void
    {
        if ($this->currentSQLMode === '') return;
        $this->database->exec("SET SESSION sql_mode=''");
    }

    private function restoreSQLMode(): void
    {
        /** @psalm-suppress TypeDoesNotContainType */
        if (!isset($this->currentSQLMode) || $this->currentSQLMode === '') return;
        $sqlMode = $this->database->quote($this->currentSQLMode);
        $this->database->exec("SET SESSION sql_mode=$sqlMode");
    }

    private function cleanup(): void
    {
        unset($this->currentSQLMode);
    }
}
