<?php

namespace HJerichen\DBUnit\Importer;

use HJerichen\DBUnit\Dataset\Converter\SQL\TableToSqlConverter;
use HJerichen\DBUnit\Dataset\Dataset;
use HJerichen\DBUnit\Dataset\Table;
use PDO;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ImporterPDO implements Importer
{
    private TableToSqlConverter $converter;
    private PDO $database;

    public function __construct(PDO $database)
    {
        $this->converter = new TableToSqlConverter();
        $this->database = $database;
    }

    public function getDatabase(): PDO
    {
        return $this->database;
    }

    public function import(Dataset $dataset): void
    {
        foreach ($dataset->getTables() as $table) {
            $this->importTable($table);
        }
    }

    private function importTable(Table $table): void
    {
        $querySets = $this->converter->getQuerySetsForTable($table);

        foreach ($querySets as $querySet) {
            $statement = $this->database->prepare($querySet->query);
            $statement->execute($querySet->parameters);
        }
    }
}