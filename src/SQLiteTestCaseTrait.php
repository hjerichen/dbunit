<?php

namespace HJerichen\DBUnit;

use HJerichen\DBUnit\Comparator\DatabaseDatasetComparator;
use HJerichen\DBUnit\DatabaseCleanup\DatabaseCleanerSQLite;
use HJerichen\DBUnit\Dataset\Attribute\DatasetAttribute;
use HJerichen\DBUnit\Dataset\Attribute\DatasetForExpected;
use HJerichen\DBUnit\Dataset\Attribute\DatasetForSetup;
use HJerichen\DBUnit\Dataset\Database\DatabaseDatasetPDO;
use HJerichen\DBUnit\Dataset\Dataset;
use HJerichen\DBUnit\Dataset\DatasetComposite;
use HJerichen\DBUnit\ForeignKey\ForeignKeyHandlerEmpty;
use HJerichen\DBUnit\Importer\ImporterPDO;
use HJerichen\DBUnit\Setup\SetupOperation;
use HJerichen\DBUnit\Setup\SetupOperationConstruct;
use HJerichen\DBUnit\StrictMode\StrictModeHandlerEmpty;
use HJerichen\DBUnit\Teardown\TeardownOperation;
use PDO;
use ReflectionMethod;

trait SQLiteTestCaseTrait
{
    abstract protected function getDatasetForSetup(): Dataset;

    abstract protected function getDatabase(): PDO;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $teardownOperation = $this->getTeardownOperation();
        $teardownOperation->execute();
    }

    protected function getDatasetForSetupFromAttribute(): ?Dataset
    {
        return $this->getDatasetFromAttribute(DatasetForSetup::class);
    }

    protected function getDatasetForExpectedFromAttribute(): ?Dataset
    {
        return $this->getDatasetFromAttribute(DatasetForExpected::class);
    }

    /** @psalm-suppress PossiblyUnusedParam */
    protected function assertDatasetEqualsCurrent(Dataset $expected): void
    {
        $database = $this->getDatabase();
        $actual = new DatabaseDatasetPDO($database);

        $comparator = new DatabaseDatasetComparator();
        $comparator->assertEquals($expected, $actual);
    }

    private function setUpDatabase(): void {
        $dataset = $this->getDatasetForSetup();

        $setupOperation = $this->getSetupOperation();
        $setupOperation->execute($dataset);
    }

    private function getSetupOperation(): SetupOperation
    {
        $database = $this->getDatabase();

        return new SetupOperationConstruct(
            new StrictModeHandlerEmpty(),
            new ForeignKeyHandlerEmpty(),
            new DatabaseCleanerSQLite($database),
            new ImporterPDO($database)
        );
    }

    private function getTeardownOperation(): TeardownOperation
    {
        $database = $this->getDatabase();
        return new DatabaseCleanerSQLite($database);
    }

    /**
     * @template T of DatasetAttribute
     * @param class-string<T> $attributeClass
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     * @psalm-suppress InternalMethod
     */
    private function getDatasetFromAttribute(string $attributeClass): ?Dataset
    {
        $reflection = new ReflectionMethod($this, $this->getName());
        $attributes = $reflection->getAttributes($attributeClass);
        if (count($attributes) === 0) return null;

        $dataset = new DatasetComposite();
        foreach ($attributes as $attribute) {
            $dataset->append($attribute->newInstance()->getDataset());
        }
        return $dataset;
    }
}