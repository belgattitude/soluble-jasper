<?php

declare(strict_types=1);

namespace JasperTest\ReportRunner;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Exception\BrokenXMLReportFileException;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportRunner\JasperReportRunner;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

class JasperReportRunnerTest extends TestCase
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
        $jasperRunner = new JasperReportRunner($this->bridgeAdapter);
        $compiled = $jasperRunner->compileReport($this->report);
        $this->assertInstanceOf(JavaObject::class, $compiled);
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

        $jasperRunner = new JasperReportRunner($this->bridgeAdapter);
        $jasperRunner->compileReport($report);
    }
}
