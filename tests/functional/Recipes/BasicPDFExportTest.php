<?php

declare(strict_types=1);

/*
 * Jasper report integration for PHP
 *
 * @link      https://github.com/belgattitude/soluble-jasper
 * @author    Vanvelthem Sébastien
 * @copyright Copyright (c) 2017 Vanvelthem Sébastien
 * @license   MIT
 */

namespace JasperTest\Functional\Recipes;

use JasperTest\Util\PDFUtils;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Proxy\Engine\Export\JRPdfExporter;
use Soluble\Jasper\Proxy\Export\SimplePdfExporterConfiguration;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportParams;
use Soluble\Jasper\ReportRunnerFactory;

class BasicPDFExportTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    public function setUp(): void
    {
        $this->ba = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testJRPdfExporterSimple(): void
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
        $jasperPrint  = $reportRunner->fillReport($jasperReport);

        $outputFile = \JasperTestsFactories::getOutputDir() . '/basic_pdf_export_test.pdf';
        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $pdfConfig = new SimplePdfExporterConfiguration($this->ba);
        $pdfConfig->setCompressed(false); // Otherwise pdfparser fails to decode
        $pdfConfig->setMetadataAuthor('Sebastien Vanvelthem');
        $pdfConfig->setMetadataCreator('belgattitude');
        $pdfConfig->setMetadataTitle('title');
        $pdfConfig->setMetadataKeywords('keywords');
        $pdfConfig->setMetadataSubject('subject');

        $jrPdfExporter = new JRPdfExporter($this->ba);
        $jrPdfExporter->setExporterInput($jasperPrint->getJavaProxiedObject());
        $jrPdfExporter->setExporterOutput(new \SplFileInfo($outputFile));
        $jrPdfExporter->setConfiguration($pdfConfig);

        $jrPdfExporter->exportReport();

        // open the pdf and check for text

        $pdfUtils = new PDFUtils($outputFile);
        $text     = $pdfUtils->getTextContent();

        self::assertStringContainsString('Soluble Jasper', $text);
        self::assertStringContainsString('Generated from unit tests', $text);

        $details = $pdfUtils->getDetails();
        self::assertSame('Sebastien Vanvelthem', $details['Author']);
        self::assertSame('belgattitude', $details['Creator']);
        self::assertSame('keywords', $details['Keywords']);
        self::assertSame('subject', $details['Subject']);
        self::assertSame('title', $details['Title']);
    }
}
