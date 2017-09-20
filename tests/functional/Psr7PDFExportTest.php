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

namespace JasperTest\Functional;

use JasperTest\Util\PDFUtils;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Exporter\Bridged\PDFExporter;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportParams;
use Soluble\Jasper\ReportRunnerFactory;

class Psr7PDFExportTest extends TestCase
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

        $pdfExporter = new PDFExporter($reportRunner, $report);

        $response = $pdfExporter->getPsr7Response();

        self::assertSame('application/pdf', $response->getHeader('content-type')['0']);
        $body = $response->getBody()->getContents();

        $text     = PDFUtils::getParsedDocumentText($body);

        self::assertContains('Soluble Jasper', $text);
        self::assertContains('Generated from unit tests', $text);
    }
}
