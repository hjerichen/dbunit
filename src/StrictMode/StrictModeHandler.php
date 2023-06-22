<?php

namespace HJerichen\DBUnit\StrictMode;

interface StrictModeHandler
{
    public function disableStrictMode(): void;

    public function restoreStrictMode(): void;
}