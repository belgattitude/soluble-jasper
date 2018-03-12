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
use Soluble\Jasper\DataSource\XmlDataSource;
use Soluble\Jasper\Exception\JavaProxiedException;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportParams;
use Soluble\Jasper\ReportRunnerFactory;

class XmlReportGenerationTest extends TestCase
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
     * @dataProvider xmlSourceProvider
     */
    public function testXmlReport(string $xmlSource): void
    {
        $reportFile = \JasperTestsFactories::getReportBaseDir() . '/13_report_xml_northwind.jrxml';

        $jsonDataSource = new XmlDataSource($xmlSource);
        $jsonDataSource->setOptions([
            XmlDataSource::PARAM_XML_DATE_PATTERN   => 'yyyy-MM-dd',
            XmlDataSource::PARAM_XML_NUMBER_PATTERN => '#,##0.##',
            XmlDataSource::PARAM_XML_TIMEZONE_ID    => 'Europe/Brussels',
            XmlDataSource::PARAM_XML_LOCALE_CODE    => 'en_US'
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

        $exportManager = $reportRunner->getExportManager($report);

        try {
            $exportManager->savePdf($output_pdf);
        } catch (JavaProxiedException $e) {
            //var_dump($e->getJvmStackTrace());
            throw $e;
        }

        $pdfUtils = new PDFUtils($output_pdf);
        $text     = $pdfUtils->getTextContent();

        self::assertContains('Customer Report From XML', $text);
        self::assertContains('PHPUNIT', $text);
        self::assertContains('Maria Anders', $text);
    }

    public function xmlSourceProvider(): array
    {
        $xmlFileSource   = \JasperTestsFactories::getDataBaseDir() . '/northwind.xml';
        $xmlUrlSource    = sprintf(
            'http://%s:%s/%s',
            EXPRESSIVE_SERVER_HOST,
            EXPRESSIVE_SERVER_PORT,
            'data/northwind.xml'
        );

        return [
            [$xmlFileSource], [$xmlUrlSource]
        ];
    }
}
