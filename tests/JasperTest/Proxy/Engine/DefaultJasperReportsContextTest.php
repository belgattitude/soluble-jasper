<?php

declare(strict_types=1);

namespace JasperTest\Proxy\Engine;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Proxy\Engine\DefaultJasperReportsContext;

class DefaultJasperReportsContextTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    public function setUp()
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testGetInstance()
    {
        $context = (new DefaultJasperReportsContext($this->bridgeAdapter))->getInstance();
        $this->assertEquals(
            'net.sf.jasperreports.engine.DefaultJasperReportsContext',
            $this->bridgeAdapter->getClassName($context)
        );
    }
}
