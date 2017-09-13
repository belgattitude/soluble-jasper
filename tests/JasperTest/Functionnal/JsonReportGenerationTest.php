<?php

declare(strict_types=1);

namespace JasperTest\Functionnal;

use JasperTest\Util\PDFUtils;
use Soluble\Jasper\DataSource\JsonDataSource;
use Soluble\Jasper\Exception\JavaProxiedException;
use Soluble\Jasper\Proxy\Engine\JasperExportManager;
use Soluble\Jasper\Report;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
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

    public function testJsonReport(): void
    {
        $reportFile = \JasperTestsFactories::getReportBaseDir() . '/10_report_json_northwind.jrxml';
        $jsonFile = \JasperTestsFactories::getDataBaseDir() . '/northwind.json';

        $jsonDataSource = new JsonDataSource($jsonFile);
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

        $output_pdf = \JasperTestsFactories::getOutputDir() . '/test_json.pdf';
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

        $output_pdf = \JasperTestsFactories::getOutputDir() . '/test_json.pdf';

        try {
            $exportManager->savePdf($output_pdf);
        } catch (JavaProxiedException $e) {
            //var_dump($e->getJvmStackTrace());
            throw $e;
        }

        $pdfUtils = new PDFUtils();
        $text = $pdfUtils->getPDFText($output_pdf);

        self::assertContains('Customer Order List', $text);
        self::assertContains('PHPUNIT', $text);
        self::assertContains('Alfreds Futterkiste', $text);
    }
}
