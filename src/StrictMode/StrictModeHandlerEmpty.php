<?php declare(strict_types=1);

namespace HJerichen\DBUnit\StrictMode;

class StrictModeHandlerEmpty implements StrictModeHandler
{
    public function disableStrictMode(): void {
    }

    public function restoreStrictMode(): void {
    }
}
