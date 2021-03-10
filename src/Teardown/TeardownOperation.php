<?php

namespace HJerichen\DBUnit\Teardown;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
interface TeardownOperation
{
    public function execute(): void;
}