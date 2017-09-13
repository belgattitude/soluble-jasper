<?php

declare(strict_types=1);

namespace JasperTest\Proxy\Engine;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Proxy\Engine\JREmptyDataSource;

class JREmptyDataSourceTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    public function setUp(): void
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testGetJavaProxiedObject(): void
    {
        $ds = new JREmptyDataSource($this->bridgeAdapter);
        self::assertEquals(
            'net.sf.jasperreports.engine.JREmptyDataSource',
            $this->bridgeAdapter->getClassName($ds->getJavaProxiedObject())
        );
    }
}
