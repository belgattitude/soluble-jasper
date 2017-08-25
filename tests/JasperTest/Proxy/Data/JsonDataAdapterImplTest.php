<?php

declare(strict_types=1);

namespace JasperTest\Proxy\Data;

use Soluble\Jasper\Proxy\Data\JsonDataAdapterImpl;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

class JsonDataAdapterImplTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    public function setUp()
    {
        $this->ba = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testProperties()
    {
        $jsonAdapter = new JsonDataAdapterImpl($this->ba);
        $jsonAdapter->setLocale('en_GB');
        $jsonAdapter->setNumberPattern('#,##0.##');
        $jsonAdapter->setLanguage(JsonDataAdapterImpl::LANGUAGE_JSONQL);
        $jsonAdapter->setDatePattern('yyyy-MM-dd');
        $jsonAdapter->setUseConnection(false);

        $javaObject = $jsonAdapter->getJavaProxiedObject();

        $this->assertEquals('#,##0.##', (string) $javaObject->getNumberPattern());
        $this->assertEquals('yyyy-MM-dd', (string) $javaObject->getDatePattern());
        $this->assertEquals('en_gb', strtolower((string) $javaObject->getLocale()));
        $this->assertEquals(JsonDataAdapterImpl::LANGUAGE_JSONQL, (string) $javaObject->getLanguage());
        $this->assertEquals(false, $javaObject->isUseConnection());
    }
}
