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

        // Step 2: Prepare report params
        $params = $this->ba->java('java.util.HashMap', []);

        // class Loader
        $jpath = $this->ba->java('java.io.File', dirname($report->getReportFile()));
        $url = $jpath->toUrl(); // Java.net.URL
        $urls = [$url];

        $classLoader = $this->ba->java('java.net.URLClassLoader', $urls);
        $params->put('REPORT_CLASS_LOADER', $classLoader);

        $params->put('BookTitle', 'Soluble Jasper');
        $params->put('BookSubTitle', 'Generated from unit tests');

        // Setting the class loader for the resource bundle
        // Assuming they are in the same directory as
        // the report file.
        /*
        $report_resource_bundle = $report->getResourceBundle();
        if ($report_resource_bundle != '') {
            $ResourceBundle = new JavaClass('java.util.ResourceBundle');
            $rb = $ResourceBundle->getBundle($report_resource_bundle, $locale, $classLoader);
            $this->params->put('REPORT_RESOURCE_BUNDLE', $rb);
        }*/

        // Step 3: Fill the report
        $fillManager = new JasperFillManager($this->ba);
        $emptyDataSource = new JREmptyDataSource($this->ba);

        $filled = $fillManager->fillReport($compiled, $params, $emptyDataSource);

        $exportManager = $this->ba->javaClass('net.sf.jasperreports.engine.JasperExportManager');

        $output_pdf = \JasperTestsFactories::getOutputDir() . '/test.pdf';
        if (file_exists($output_pdf)) {
            unlink($output_pdf);
        }

        $exportManager->exportReportToPdfFile($filled, $output_pdf);
        @chmod($output_pdf, 0666);
        $this->assertFileExists($output_pdf);

        // open the pdf and check for text

        $pdfUtils = new PDFUtils();
        $text = $pdfUtils->getPDFText($output_pdf);

        $this->assertContains('Soluble Jasper', $text);
        $this->assertContains('Generated from unit tests', $text);
    }
}
