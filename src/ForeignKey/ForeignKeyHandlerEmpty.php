<?php declare(strict_types=1);

namespace HJerichen\DBUnit\ForeignKey;

class ForeignKeyHandlerEmpty implements ForeignKeyHandler
{
    public function disableForeignKeyCheck(): void
    {
    }

    public function enableForeignKeyCheck(): void
    {
    }
}
