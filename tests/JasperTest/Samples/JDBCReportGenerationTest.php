<?php

declare(strict_types=1);

namespace JasperTest\Samples;

use JasperTest\Util\PDFUtils;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\DataSource\JdbcDataSource;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportParams;
use Soluble\Jasper\ReportRunnerFactory;

class JDBCReportGenerationTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    public function setUp()
    {
        if (\JasperTestsFactories::isJdbcTestsEnabled()) {
            $this->markTestSkipped(
                'Skipping JDBCReportGeneration tests, enable option in phpunit.xml '
            );
        }
        $this->ba = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testJDBCReport()
    {
        $reportFile = \JasperTestsFactories::getReportBaseDir() . '/08_report_test_jdbc.jrxml';

        $reportRunner = ReportRunnerFactory::getBridgedReportRunner($this->ba);

        $report = new Report(
                $reportFile,
                new ReportParams(),
                new JdbcDataSource(
                        \JasperTestsFactories::getJdbcDsn(),
                    'com.mysql.jdbc.Driver'
                )
            );

        $exportManager = $reportRunner->getExportManager($report);

        $output_pdf = \JasperTestsFactories::getOutputDir() . '/test_jdbc.pdf';
        if (file_exists($output_pdf)) {
            unlink($output_pdf);
        }

        $exportManager->savePdf($output_pdf);

        // open the pdf and check for text

        $pdfUtils = new PDFUtils();
        $text = $pdfUtils->getPDFText($output_pdf);

        $this->assertContains('JDBC mysql report test', $text);
        $this->assertContains('Congas', $text);
    }
}
