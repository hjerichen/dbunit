<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Dataset\Converter\SQL;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class SqlQuerySet
{
    public string $query;
    public array $parameters;
}