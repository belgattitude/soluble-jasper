<?php

declare(strict_types=1);

namespace JasperTest\DataSource;

use PHPUnit\Framework\TestCase;
use Soluble\Jasper\DataSource\JsonDataSource;

class JsonDataSourceTest extends TestCase
{
    public function setUp()
    {
    }

    public function testOptions()
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

        $this->assertEquals($jsonFile, $options[JsonDataSource::PARAM_JSON_SOURCE]);
        $this->assertEquals('en_US', $options[JsonDataSource::PARAM_JSON_LOCALE_CODE]);
        $this->assertEquals('Europe/Brussels', $options[JsonDataSource::PARAM_JSON_TIMEZONE_ID]);
        $this->assertEquals('#,##0.##', $options[JsonDataSource::PARAM_JSON_NUMBER_PATTERN]);
        $this->assertEquals('yyyy-MM-dd', $options[JsonDataSource::PARAM_JSON_DATE_PATTERN]);
    }
}
