<?php

declare(strict_types=1);

namespace JasperTest\Proxy\Engine;

use JasperTestsFactories;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Report\ReportInterface;
use Soluble\Jasper\Proxy\Engine\JasperReport;
use Soluble\Jasper\Report;

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

    public function testGetStatus()
    {
        $report = new Report(JasperTestsFactories::getDefaultReportFile());
        $jasperReport = new JasperReport(
            $this->bridgeAdapter->java('net.sf.jasperreports.engine.JasperReport'),
            $report
        );
        $this->assertEquals(ReportInterface::STATUS_COMPILED, $jasperReport->getStatus());
    }
}
