<?php

declare(strict_types=1);

namespace JasperTest\Functionnal;

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

    public function setUp(): void
    {
        $this->ba = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testWithResourceFR(): void
    {
        $reportFile = \JasperTestsFactories::getReportBaseDir() . '/11_report_resource.jrxml';

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

        self::assertContains('TestResources.fr', $text);
        self::assertContains('subtitle fr', $text);
    }

    public function testWithResourceMissingZH(): void
    {
        $reportFile = \JasperTestsFactories::getReportBaseDir() . '/11_report_resource.jrxml';

        $reportRunner = ReportRunnerFactory::getBridgedReportRunner($this->ba);

        $report = new Report(
            $reportFile,
            new ReportParams([
                JRParameter::REPORT_LOCALE => $this->ba->java('java.util.Locale', 'zh')
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

        self::assertContains('TestResources.default', $text);
        self::assertContains('Subtitle default', $text);
    }
}