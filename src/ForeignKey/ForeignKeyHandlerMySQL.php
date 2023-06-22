<?php declare(strict_types=1);

namespace HJerichen\DBUnit\ForeignKey;

use PDO;

class ForeignKeyHandlerMySQL implements ForeignKeyHandler
{
    public function __construct(
        private readonly PDO $database
    ) {
    }

    public function disableForeignKeyCheck(): void
    {
        $this->database->exec('SET foreign_key_checks=0');
    }

    public function enableForeignKeyCheck(): void
    {
        $this->database->exec('SET foreign_key_checks=1');
    }
}
