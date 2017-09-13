<?php

declare(strict_types=1);

namespace JasperTest\Proxy\Engine;

use JasperTestsFactories;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Report\ReportInterface;
use Soluble\Jasper\Proxy\Engine\JasperPrint;
use Soluble\Jasper\Report;

class JasperPrintTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    public function setUp(): void
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testGetStatus(): void
    {
        $report = new Report(JasperTestsFactories::getDefaultReportFile());
        $jasperReport = new JasperPrint(
            $this->bridgeAdapter->java('net.sf.jasperreports.engine.JasperReport'),
            $report
        );
        $this->assertEquals(ReportInterface::STATUS_FILLED, $jasperReport->getStatus());
    }
}
