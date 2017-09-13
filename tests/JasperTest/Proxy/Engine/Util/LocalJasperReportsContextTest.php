<?php

declare(strict_types=1);

namespace JasperTest\Proxy\Engine\Util;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Context\DefaultClassLoader;
use Soluble\Jasper\Context\DefaultFileResolver;
use Soluble\Jasper\Proxy\Engine\DefaultJasperReportsContext;
use Soluble\Jasper\Proxy\Engine\Util\LocalJasperReportsContext;

class LocalJasperReportsContextTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    public function setUp(): void
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testBasic(): void
    {
        $context = new LocalJasperReportsContext($this->bridgeAdapter);
        $context->setProperty('cool', 'test');
        self::assertEquals('test', $context->getProperty('cool'));

        $context->setPropertiesMap(['prop1' => '1', 'prop2' => 2]);
        self::assertEquals('1', $context->getProperty('prop1'));
        // @todo
        //self::assertEquals(2,  $context->getProperty('prop2')->intValue());

        $context->removeProperty('cool');
        self::assertNull($context->getProperty('cool'));
    }

    public function testGetProxiedObject(): void
    {
        $context = new LocalJasperReportsContext(
            $this->bridgeAdapter,
            (new DefaultJasperReportsContext($this->bridgeAdapter))->getJavaProxiedObject()
        );

        $proxied = $context->getJavaProxiedObject();
        self::assertEquals('net.sf.jasperreports.engine.util.LocalJasperReportsContext', $this->bridgeAdapter->getClassName($proxied));
    }

    public function testSetClassLoader(): void
    {
        $context = new LocalJasperReportsContext($this->bridgeAdapter);
        $context->setClassLoader(
            (new DefaultClassLoader($this->bridgeAdapter))
                ->getClassLoader(
                    [\JasperTestsFactories::getReportBaseDir()]
                )
        );
        self::assertTrue(true);
    }

    public function testSetFileResolver(): void
    {
        $context = new LocalJasperReportsContext($this->bridgeAdapter);
        $context->setFileResolver(
            (new DefaultFileResolver($this->bridgeAdapter))
                ->getFileResolver(
                    [\JasperTestsFactories::getReportBaseDir()]
                )
        );
        self::assertTrue(true);
    }
}
