<?php

declare(strict_types=1);

namespace JasperTest\Runner\Bridged\Proxy;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Runner\Bridged\Proxy\JasperReport;

class JasperReportTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    public function setUp()
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testGetJavaProxiedObject()
    {
        $jasperReport = new JasperReport($this->bridgeAdapter);
        $this->assertEquals(
            'net.sf.jasperreports.engine.JasperReport',
            $this->bridgeAdapter->getClassName($jasperReport->getJavaProxiedObject())
        );
    }
}
