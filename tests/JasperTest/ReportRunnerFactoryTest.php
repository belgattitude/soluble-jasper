<?php

declare(strict_types=1);

namespace JasperTest;

use PHPUnit\Framework\TestCase;
use Soluble\Jasper\ReportRunnerFactory;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

class ReportRunnerFactoryTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    public function setUp()
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testGetJasperReportRunner()
    {
        $jasperRunner = ReportRunnerFactory::getBridgedJasperReportRunner($this->bridgeAdapter);
        $this->assertTrue(true);
    }
}
