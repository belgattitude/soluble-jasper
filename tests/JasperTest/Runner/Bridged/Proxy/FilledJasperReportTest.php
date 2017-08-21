<?php

declare(strict_types=1);

namespace JasperTest\Runner\Bridged\Proxy;

use JasperTestsFactories;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Report\ReportInterface;
use Soluble\Jasper\Runner\Bridged\Proxy\FilledJasperReport;
use Soluble\Jasper\Report;

class FilledJasperReportTest extends TestCase
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
        $compiled = new FilledJasperReport(
            $this->bridgeAdapter->java('net.sf.jasperreports.engine.JasperReport'),
            $report
        );
        $this->assertEquals(ReportInterface::STATUS_FILLED, $compiled->getStatus());
    }
}
