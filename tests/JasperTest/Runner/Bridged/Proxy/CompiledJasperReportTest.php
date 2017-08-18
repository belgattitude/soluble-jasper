<?php

declare(strict_types=1);

namespace JasperTest\Runner\Bridged\Proxy;

use JasperTestsFactories;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Runner\Bridged\Proxy\CompiledJasperReport;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportInterface;

class CompiledJasperReportTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    public function setUp()
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testGetStatus()
    {
        $report = new Report(JasperTestsFactories::getDefaultReportFile());
        $compiled = new CompiledJasperReport(
            $this->bridgeAdapter->java('net.sf.jasperreports.engine.JasperReport'),
            $report
        );
        $this->assertEquals(ReportInterface::STATUS_COMPILED, $compiled->getStatus());
    }
}