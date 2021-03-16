<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Teardown;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
interface TeardownOperation
{
    public function execute(): void;
}