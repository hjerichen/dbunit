<?php

namespace HJerichen\DBUnit\ForeignKey;

interface ForeignKeyHandler
{
    public function disableForeignKeyCheck(): void;
    public function enableForeignKeyCheck(): void;
}