<?php

declare(strict_types=1);

namespace JasperTest\Runner;

use PHPUnit\Framework\TestCase;
use Soluble\Jasper\Exception\BrokenXMLReportFileException;
use Soluble\Jasper\Proxy\Engine\JasperReport;
use Soluble\Jasper\Report;
use Soluble\Jasper\Runner\BridgedReportRunner;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

class BridgedJasperReportRunnerTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    /**
     * @var Report
     */
    protected $report;

    public function setUp()
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
        $this->report = new Report(\JasperTestsFactories::getDefaultReportFile());
    }

    public function testCompile()
    {
        $jasperRunner = new BridgedReportRunner($this->bridgeAdapter);
        $jasperReport = $jasperRunner->compileReport($this->report);
        $this->assertInstanceOf(JasperReport::class, $jasperReport);
    }

    public function testCompileThrowsBrokenXmlException()
    {
        $reportFile = \JasperTestsFactories::getBrokenXMLReportFile();

        $report = new Report($reportFile);

        $this->expectException(BrokenXMLReportFileException::class);
        $this->expectExceptionMessageRegExp(
            sprintf(
                '#The report file "%s" cannot be parsed or not in jasper format#',
                $reportFile
            )
        );

        $jasperRunner = new BridgedReportRunner($this->bridgeAdapter);
        $jasperRunner->compileReport($report);
    }
}
