<?php

declare(strict_types=1);

namespace JasperTest\Proxy\Engine\Util;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Proxy\Engine\Util\LocalJasperReportsContext;

class LocalJasperReportsContextTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    public function setUp()
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testBasic()
    {
        $context = new LocalJasperReportsContext($this->bridgeAdapter);
        $context->setProperty('cool', 'test');
        $this->assertEquals('test', $context->getProperty('cool'));
    }
}
