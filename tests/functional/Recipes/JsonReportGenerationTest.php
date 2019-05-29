<?php

declare(strict_types=1);

/*
 * Jasper report integration for PHP
 *
 * @link      https://github.com/belgattitude/soluble-jasper
 * @author    Vanvelthem Sébastien
 * @copyright Copyright (c) 2017-2019 Vanvelthem Sébastien
 * @license   MIT
 */

namespace JasperTest\Functional\Recipes;

use JasperTest\Util\PDFUtils;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\DataSource\JsonDataSource;
use Soluble\Jasper\Exception\JavaProxiedException;
use Soluble\Jasper\Proxy\Engine\JasperExportManager;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportParams;
use Soluble\Jasper\ReportRunnerFactory;

class JsonReportGenerationTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    public function setUp(): void
    {
        $this->ba = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    /**
     * @dataProvider jsonSourceProvider
     */
    public function testJsonReport(string $jsonSource): void
    {
        $reportFile = \JasperTestsFactories::getReportBaseDir() . '/10_report_json_northwind.jrxml';

        $jsonDataSource = new JsonDataSource($jsonSource);
        $jsonDataSource->setOptions([
            JsonDataSource::PARAM_JSON_DATE_PATTERN   => 'yyyy-MM-dd',
            JsonDataSource::PARAM_JSON_NUMBER_PATTERN => '#,##0.##',
            JsonDataSource::PARAM_JSON_TIMEZONE_ID    => 'Europe/Brussels',
            JsonDataSource::PARAM_JSON_LOCALE_CODE    => 'en_US'
        ]);

        $reportParams = new ReportParams([
            'LOGO_FILE'    => \JasperTestsFactories::getReportBaseDir() . '/assets/wave.png',
            'REPORT_TITLE' => 'PHPUNIT'
        ]);

        $reportRunner = ReportRunnerFactory::getBridgedReportRunner($this->ba);

        $report = new Report($reportFile, $reportParams, $jsonDataSource);

        $output_pdf = \JasperTestsFactories::getOutputDir() . '/' . basename($reportFile, '.jrxml') . '.pdf';
        if (file_exists($output_pdf)) {
            unlink($output_pdf);
        }

        /*
        $jasperReport = $reportRunner->compileReport($report);
        $jasperPrint = $reportRunner->fillReport($jasperReport);
        $exportManager = new JasperExportManager($this->ba);
        $exportManager->exportReportToPdfFile($jasperPrint->getJavaProxiedObject(), $output_pdf);
*/

        $exportManager = $reportRunner->getExportManager($report);

        try {
            $exportManager->savePdf($output_pdf);
        } catch (JavaProxiedException $e) {
            //var_dump($e->getJvmStackTrace());
            throw $e;
        }

        $pdfUtils = new PDFUtils($output_pdf);
        $text     = $pdfUtils->getTextContent();

        self::assertStringContainsString('Customer Order List', $text);
        self::assertStringContainsString('PHPUNIT', $text);
        self::assertStringContainsString('Alfreds Futterkiste', $text);
    }

    public function jsonSourceProvider(): array
    {
        $jsonFileSource   = \JasperTestsFactories::getDataBaseDir() . '/northwind.json';
        $jsonUrlSource    = sprintf(
            'http://%s:%s/%s',
            EXPRESSIVE_SERVER_HOST,
            EXPRESSIVE_SERVER_PORT,
            'data/northwind.json'
        );

        return [
            [$jsonFileSource], [$jsonUrlSource]
        ];
    }
}
