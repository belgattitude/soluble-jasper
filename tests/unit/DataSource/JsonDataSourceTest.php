<?php

declare(strict_types=1);

/*
 * Jasper report integration for PHP
 *
 * @link      https://github.com/belgattitude/soluble-jasper
 * @author    Vanvelthem Sébastien
 * @copyright Copyright (c) 2017-2019 Vanvelthem Sébastien
 * @license   MIT
 */

namespace JasperTest\DataSource;

use PHPUnit\Framework\TestCase;
use Soluble\Jasper\DataSource\JsonDataSource;

class JsonDataSourceTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testOptions(): void
    {
        $jsonFile = \JasperTestsFactories::getDataBaseDir() . '/northwind.json';

        $jsonDataSource = new JsonDataSource($jsonFile);
        $jsonDataSource->setOptions([
            JsonDataSource::PARAM_JSON_DATE_PATTERN   => 'yyyy-MM-dd',
            JsonDataSource::PARAM_JSON_NUMBER_PATTERN => '#,##0.##',
            JsonDataSource::PARAM_JSON_TIMEZONE_ID    => 'Europe/Brussels',
            JsonDataSource::PARAM_JSON_LOCALE_CODE    => 'en_US'
        ]);

        $options = $jsonDataSource->getOptions();

        self::assertEquals($jsonFile, $options[JsonDataSource::PARAM_JSON_SOURCE]);
        self::assertEquals('en_US', $options[JsonDataSource::PARAM_JSON_LOCALE_CODE]);
        self::assertEquals('Europe/Brussels', $options[JsonDataSource::PARAM_JSON_TIMEZONE_ID]);
        self::assertEquals('#,##0.##', $options[JsonDataSource::PARAM_JSON_NUMBER_PATTERN]);
        self::assertEquals('yyyy-MM-dd', $options[JsonDataSource::PARAM_JSON_DATE_PATTERN]);
    }
}
