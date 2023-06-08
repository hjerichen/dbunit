<?php declare(strict_types=1);

namespace HJerichen\DBUnit\Tests\Unit\Classes\Importer;

use HJerichen\DBUnit\Importer\ImporterPDO;
use PDO;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ImporterPDOTest extends TestCase
{
    use ProphecyTrait;

    private ImporterPDO $importer;
    /** @var ObjectProphecy<PDO> */
    private ObjectProphecy $database;

    protected function setUp(): void
    {
        parent::setUp();
        $this->database = $this->prophesize(PDO::class);

        $this->importer = new ImporterPDO($this->database->reveal());
    }

    /* TESTS */

    public function testGetDatabase(): void
    {
        $expected = $this->database->reveal();
        $actual = $this->importer->getDatabase();
        self::assertEquals($expected, $actual);
    }
}