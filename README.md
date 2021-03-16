[![Continuous Integration](https://github.com/hjerichen/dbunit/workflows/Continuous%20Integration/badge.svg?branch=master)](https://github.com/hjerichen/prophecy-php/actions)
[![Coverage Status](https://coveralls.io/repos/github/hjerichen/dbunit/badge.svg?branch=master)](https://coveralls.io/github/hjerichen/dbunit?branch=master)

# DBUnit
An alternative to phpunit/dbunit.  
Supports PDO Drivers but currently only tested with MySQL.

## Installation
Use [Composer](https://getcomposer.org/):
```sh
composer require --dev hjerichen/dbunit
```

## Usage
Use the trait MySQLTestCaseTrait in PHPUnit Test Cases to test with MySQL, or build an abstract DatabaseTestCase class for your Test classes.

```php
<?php

use HJerichen\DBUnit\Dataset\Dataset;
use HJerichen\DBUnit\Dataset\DatasetArray;
use HJerichen\DBUnit\MySQLTestCaseTrait;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\ComparisonFailure;

class MySQLTest extends TestCase
{
    use MySQLTestCaseTrait;

    private PDO $database;

    protected function getDatabase(): PDO
    {
        if (!isset($this->database)) {
            $this->database = new PDO(MYSQL_DSN, MYSQL_USER, MYSQL_PASS);
        }
        return $this->database;
    }

    protected function getDatasetForSetup(): Dataset
    {
        return new DatasetArray([
            'product' => [
                ['id' => 1, 'ean' => '123', 'stock' => 0],
                ['id' => 2, 'ean' => '456', 'stock' => 10],
            ]
        ]);
    }

    /* TESTS */

    public function testImportAndCompare(): void
    {
        $expected = new DatasetArray([
            'product' => [
                ['id' => 1, 'ean' => '123', 'stock' => 0],
                ['id' => 2, 'ean' => '456', 'stock' => 10],
            ]
        ]);
        
        try {
            $this->assertDatasetEqualsCurrent($expected);
        } catch (ComparisonFailure $failure) {
            throw new ExpectationFailedException($failure->getMessage(), $failure);
        }
    }
}
```

### Datasets

Supported Datasets are:  
DatasetArray  
DatasetYaml  

## License and authors

This project is free and under the MIT Licence.
Responsible for this project is Heiko Jerichen (heiko@jerichen.de).