<?php

declare(strict_types=1);

namespace JasperTest;

use Soluble\Jasper\Exception\ReportFileNotFoundException;
use Soluble\Jasper\Report;
use PHPUnit\Framework\TestCase;
use Soluble\Jasper\Report\ReportInterface;

class ReportTest extends TestCase
{
    /**
     * @var string
     */
    protected $reportFile;

    public function setUp()
    {
        $this->reportFile = \JasperTestsFactories::getReportBaseDir() . '/MyReports/01_report_test_wavebook_cover.jrxml';
    }

    public function testConstructThrowsMissingReportFileException()
    {
        $this->expectException(ReportFileNotFoundException::class);
        new Report('/sdkfjlksdjf/sdfsdfs.jrxml');
    }

    public function testGetReportFile()
    {
        $report = new Report($this->reportFile);
        $this->assertFileEquals($this->reportFile, $report->getReportFile());
    }

    public function testGetStatus()
    {
        $report = new Report($this->reportFile);
        $this->assertEquals(ReportInterface::STATUS_FRESH, $report->getStatus());
    }
}
