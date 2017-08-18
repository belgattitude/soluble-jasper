<?php

declare(strict_types=1);

namespace JasperTest\Samples;

use JasperTest\Util\PDFUtils;
use Soluble\Jasper\Proxy\V6\JasperFillManager;
use Soluble\Jasper\Proxy\V6\JREmptyDataSource;
use Soluble\Jasper\Report;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\ReportParams;
use Soluble\Jasper\ReportRunnerFactory;

class ReportGenerationTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    public function setUp()
    {
        $this->ba = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testDefaultReport()
    {
        $reportFile = \JasperTestsFactories::getDefaultReportFile();

        $report = new Report($reportFile);
        $runner = ReportRunnerFactory::getJasperReportRunner($this->ba);

        // Step 1: Compile
        $compiled = $runner->compileReport($report);

        $reportParams = new ReportParams();
        $reportParams->put('BookTitle', 'Soluble Jasper');
        $reportParams->put('BookSubTitle', 'Generated from unit tests');

        // Step 3: Fill the report
        $fillManager = new JasperFillManager($this->ba);
        $emptyDataSource = new JREmptyDataSource($this->ba);

        $filled = $runner->fillReport($compiled, $reportParams, $emptyDataSource);
        //$filled = $fillManager->fillReport($compiled, $params, $emptyDataSource);

        $exportManager = $this->ba->javaClass('net.sf.jasperreports.engine.JasperExportManager');

        $output_pdf = \JasperTestsFactories::getOutputDir() . '/test.pdf';
        if (file_exists($output_pdf)) {
            unlink($output_pdf);
        }

        $exportManager->exportReportToPdfFile($filled->getJavaProxiedObject(), $output_pdf);
        @chmod($output_pdf, 0666);
        $this->assertFileExists($output_pdf);

        // open the pdf and check for text

        $pdfUtils = new PDFUtils();
        $text = $pdfUtils->getPDFText($output_pdf);

        $this->assertContains('Soluble Jasper', $text);
        $this->assertContains('Generated from unit tests', $text);
    }
}
