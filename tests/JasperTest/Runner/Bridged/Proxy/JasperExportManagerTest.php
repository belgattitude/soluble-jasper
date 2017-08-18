<?php

declare(strict_types=1);

namespace JasperTest\Runner\Bridged\Proxy;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Runner\Bridged\Proxy\JasperExportManager;

class JasperExportManagerTest extends TestCase
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
        $em = new JasperExportManager($this->bridgeAdapter);
        $this->assertEquals(
            'net.sf.jasperreports.engine.JasperExportManager',
            $this->bridgeAdapter->getClassName($em->getJavaProxiedObject())
        );
    }
}
