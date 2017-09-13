<?php

declare(strict_types=1);

namespace JasperTest\Functionnal;

use Soluble\Jasper\Report;
use Soluble\Jasper\ReportParams;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\ReportRunnerFactory;
use JasperTest\Util\PDFUtils;

class BasicReportGenerationTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    public function setUp(): void
    {
        $this->ba = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testDefaultReport(): void
    {
        $reportFile = \JasperTestsFactories::getReportBaseDir() . '/01_report_default.jrxml';

        $reportRunner = ReportRunnerFactory::getBridgedReportRunner($this->ba);

        $report = new Report(
                    $reportFile,
                    new ReportParams([
                        'BookTitle'    => 'Soluble Jasper',
                        'BookSubTitle' => 'Generated from unit tests'
                    ])
        );

        $jasperReport = $reportRunner->compileReport($report);
        $filled = $reportRunner->fillReport($jasperReport);

        $exportManager = $reportRunner->getExportManager($report);

        $output_pdf = \JasperTestsFactories::getOutputDir() . '/test.pdf';
        if (file_exists($output_pdf)) {
            unlink($output_pdf);
        }

        $exportManager->savePdf($output_pdf);

        // open the pdf and check for text

        $pdfUtils = new PDFUtils();
        $text = $pdfUtils->getPDFText($output_pdf);

        self::assertContains('Soluble Jasper', $text);
        self::assertContains('Generated from unit tests', $text);
    }
}
