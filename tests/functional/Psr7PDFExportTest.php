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
use Psr\Http\Message\ResponseInterface;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Exporter\PDFExporter;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportParams;
use Soluble\Jasper\ReportRunnerFactory;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\JsonResponse;

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

    public function psr7Responses(): array
    {
        return [
            [null, null],
            [(new Response())->withHeader('cool', 'hello'), ['cool' => 'hello']],
            [(new JsonResponse([]))->withHeader('cool', 'hello'), ['cool' => 'hello']]
        ];
    }

    /**
     * @dataProvider psr7Responses
     */
    public function testPdfExporterPsr7(ResponseInterface $initialResponse=null, array $expectedHeaders=null): void
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

        $pdfExporter = new PDFExporter($report, $reportRunner);

        $pdfConfig = [];
        $response  = $pdfExporter->getPsr7Response($pdfConfig, $initialResponse);

        if ($expectedHeaders !== null) {
            foreach ($expectedHeaders as $name => $value) {
                self::assertTrue($response->hasHeader($name));
                self::assertSame($value, $response->getHeader($name)[0]);
            }
        }

        self::assertSame('application/pdf', $response->getHeader('content-type')['0']);
        $body = $response->getBody()->getContents();

        $text     = PDFUtils::getParsedDocumentText($body);

        self::assertContains('Soluble Jasper', $text);
        self::assertContains('Generated from unit tests', $text);
    }
}
