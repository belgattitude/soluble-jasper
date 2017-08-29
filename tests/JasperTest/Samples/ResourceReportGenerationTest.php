<?php

declare(strict_types=1);

namespace JasperTest\Samples;

use Soluble\Jasper\JRParameter;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportParams;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\ReportRunnerFactory;
use JasperTest\Util\PDFUtils;

class ResourceReportGenerationTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    public function setUp()
    {
        $this->ba = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testWithResourceEn()
    {
        $reportFile = \JasperTestsFactories::getReportBaseDir() . '/11_report_test_resource.jrxml';

        $reportRunner = ReportRunnerFactory::getBridgedReportRunner($this->ba);

        $report = new Report(
                    $reportFile,
                    new ReportParams([
                        JRParameter::REPORT_LOCALE => $this->ba->java('java.util.Locale', 'fr')
                    ])
        );

        $jasperReport = $reportRunner->compileReport($report);
        $filled = $reportRunner->fillReport($jasperReport);

        $exportManager = $reportRunner->getExportManager($report);

        $output_pdf = \JasperTestsFactories::getOutputDir() . '/test_resource.pdf';
        if (file_exists($output_pdf)) {
            unlink($output_pdf);
        }

        $exportManager->savePdf($output_pdf);

        // open the pdf and check for text

        $pdfUtils = new PDFUtils();
        $text = $pdfUtils->getPDFText($output_pdf);

        $this->assertContains('TestResources.fr', $text);
        $this->assertContains('subtitle fr', $text);
    }
}
