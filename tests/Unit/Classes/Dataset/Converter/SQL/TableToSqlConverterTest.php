<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Tests\Unit\Classes\Dataset\Converter\SQL;

use HJerichen\DBUnit\Dataset\Converter\SQL\SqlQuerySet;
use HJerichen\DBUnit\Dataset\Converter\SQL\TableToSqlConverter;
use HJerichen\DBUnit\Dataset\Table;
use PHPUnit\Framework\TestCase;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class TableToSqlConverterTest extends TestCase
{
    private TableToSqlConverter $converter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = new TableToSqlConverter();
    }

    /* TESTS */

    public function testForSimpleTable(): void
    {
        $valueSet = [
            [
                'id' => 1,
                'title' => 'some title'
            ],
        ];
        $table = new Table('someTable', $valueSet);

        $querySet = new SqlQuerySet;
        $querySet->parameters = [':id_0' => 1, ':title_0' => 'some title'];
        $querySet->query = 'INSERT INTO someTable (`id`, `title`) VALUES (:id_0, :title_0);';

        $expected = [$querySet];
        $actual = $this->converter->getQuerySetsForTable($table);
        self::assertEquals($expected, $actual);
    }

    public function testForColumnMismatch(): void
    {
        $valueSet = [
            [
                'id' => 1,
                'title' => 'some title'
            ],
            [
                'id' => 2,
                'title' => 'some title 2'
            ],
            [
                'id' => 3,
                'text' => 'some text'
            ],
        ];
        $table = new Table('someTable', $valueSet);

        $querySet1 = new SqlQuerySet;
        $querySet1->parameters = [':id_0' => 1, ':title_0' => 'some title', ':id_1' => 2, ':title_1' => 'some title 2'];
        $querySet1->query = 'INSERT INTO someTable (`id`, `title`) VALUES (:id_0, :title_0), (:id_1, :title_1);';

        $querySet2 = new SqlQuerySet;
        $querySet2->parameters = [':id_0' => 3, ':text_0' => 'some text'];
        $querySet2->query = 'INSERT INTO someTable (`id`, `text`) VALUES (:id_0, :text_0);';

        $expected = [$querySet1, $querySet2];
        $actual = $this->converter->getQuerySetsForTable($table);
        self::assertEquals($expected, $actual);
    }
}