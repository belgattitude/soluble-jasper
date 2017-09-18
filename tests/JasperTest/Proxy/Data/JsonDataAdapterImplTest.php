<?php

declare(strict_types=1);

/*
 * Jasper report integration for PHP
 *
 * @link      https://github.com/belgattitude/soluble-jasper
 * @author    Vanvelthem Sébastien
 * @copyright Copyright (c) 2017 Vanvelthem Sébastien
 * @license   MIT
 */

namespace JasperTest\Proxy\Data;

use Soluble\Jasper\Exception\FileNotFoundException;
use Soluble\Jasper\Exception\InvalidArgumentException;
use Soluble\Jasper\Proxy\Data\JsonDataAdapterImpl;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

class JsonDataAdapterImplTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    public function setUp(): void
    {
        $this->ba = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testProperties(): void
    {
        $jsonAdapter = new JsonDataAdapterImpl($this->ba);
        $jsonAdapter->setLocale('en_GB');
        $jsonAdapter->setNumberPattern('#,##0.##');
        $jsonAdapter->setLanguage(JsonDataAdapterImpl::LANGUAGE_JSONQL);
        $jsonAdapter->setDatePattern('yyyy-MM-dd');
        $jsonAdapter->setUseConnection(false);

        $javaObject = $jsonAdapter->getJavaProxiedObject();

        self::assertEquals('#,##0.##', (string) $javaObject->getNumberPattern());
        self::assertEquals('yyyy-MM-dd', (string) $javaObject->getDatePattern());
        self::assertEquals('en_gb', strtolower((string) $javaObject->getLocale()));
        self::assertEquals(JsonDataAdapterImpl::LANGUAGE_JSONQL, (string) $javaObject->getLanguage());
        self::assertEquals(false, $javaObject->isUseConnection());

        $jsonAdapter->setLanguage(JsonDataAdapterImpl::LANGUAGE_JSON);
        self::assertEquals(JsonDataAdapterImpl::LANGUAGE_JSON, (string) $javaObject->getLanguage());
    }

    public function testSetFileName(): void
    {
        $jsonFile = \JasperTestsFactories::getDataBaseDir() . '/northwind.json';
        $jsonAdapter = new JsonDataAdapterImpl($this->ba);
        $jsonAdapter->setFileName($jsonFile);
        $javaObject = $jsonAdapter->getJavaProxiedObject();
        self::assertEquals($jsonFile, (string) $javaObject->getFileName());
    }

    public function testSetFileNameThrowsException(): void
    {
        $this->expectException(FileNotFoundException::class);
        $jsonFile = \JasperTestsFactories::getDataBaseDir() . '/filenotexist.json';
        $jsonAdapter = new JsonDataAdapterImpl($this->ba);
        $jsonAdapter->setFileName($jsonFile);
    }

    public function testSetLanguageThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $jsonAdapter = new JsonDataAdapterImpl($this->ba);
        $jsonAdapter->setLanguage('notjson');
    }
}
