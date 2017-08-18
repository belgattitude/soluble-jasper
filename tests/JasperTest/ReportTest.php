<?php

declare(strict_types=1);

namespace JasperTest;

use Soluble\Jasper\Proxy\V6\JasperCompileManager;
use Soluble\Jasper\Proxy\V6\JasperFillManager;
use Soluble\Jasper\Proxy\V6\JREmptyDataSource;
use Soluble\Jasper\Report;
use PHPUnit\Framework\TestCase;

class ReportTest extends TestCase
{
    /**
     * @var string
     */
    protected $report;

    public function setUp()
    {
        $this->report = \JasperTestsFactories::getReportBaseDir() . '/MyReports/01_report_test_wavebook_cover.jrxml';
    }

    public function testGetReportFile()
    {
        $report = new Report($this->report);
        $this->assertFileEquals($this->report, $report->getReportFile());
    }

    public function testTemp()
    {
        $ba = \JasperTestsFactories::getJavaBridgeAdapter();
        $report = new Report($this->report);

        //$runner = new ReportRunnerJapha($ba);
        //$compiled = $runner->compileReport($report);
        //echo $ba->getDriver()->inspect($compiled);

        $reportFile = $report->getReportFile();

        $compileManager = new JasperCompileManager($ba);
        $compiled = $compileManager->compileReport($reportFile);

        // params
        $params = $ba->java('java.util.HashMap', []);

        // class Loader
        $jpath = $ba->java('java.io.File', dirname($report->getReportFile()));
        $url = $jpath->toUrl(); // Java.net.URL
        $urls = [$url];

        $classLoader = $ba->java('java.net.URLClassLoader', $urls);
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

        $fillManager = new JasperFillManager($ba);
        $emptyDataSource = new JREmptyDataSource($ba);

        $filled = $fillManager->fillReport($compiled, $params, $emptyDataSource);

        $exportManager = $ba->javaClass('net.sf.jasperreports.engine.JasperExportManager');

        $output_pdf = \JasperTestsFactories::getOutputDir() . '/test.pdf';
        if (file_exists($output_pdf)) {
            unlink($output_pdf);
        }

        $exportManager->exportReportToPdfFile($filled, $output_pdf);
        @chmod($output_pdf, 0666);
        $this->assertFileExists($output_pdf);

        // open the pdf and check for text
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($output_pdf);

        $pages = $pdf->getPages();

        $text = '';
        // Loop over each page to extract text.
        foreach ($pages as $page) {
            $text .= $page->getText();
        }

        $this->assertContains('Soluble Jasper', $text);
        $this->assertContains('Generated from unit tests', $text);
    }
}
