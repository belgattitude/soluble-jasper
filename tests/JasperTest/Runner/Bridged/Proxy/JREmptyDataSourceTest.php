<?php

declare(strict_types=1);

namespace JasperTest\Runner\Bridged\Proxy;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Runner\Bridged\Proxy\JREmptyDataSource;

class JREmptyDataSourceTest extends TestCase
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
        $ds = new JREmptyDataSource($this->bridgeAdapter);
        $this->assertEquals(
            'net.sf.jasperreports.engine.JREmptyDataSource',
            $this->bridgeAdapter->getClassName($ds->getJavaProxiedObject())
        );
    }
}
