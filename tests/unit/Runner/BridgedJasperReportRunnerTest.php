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

namespace JasperTest\Runner;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\DataSource\JsonDataSource;
use Soluble\Jasper\Exception\BrokenJsonDataSourceException;
use Soluble\Jasper\Exception\BrokenXMLReportFileException;
use Soluble\Jasper\Proxy\Engine\JasperReport;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportParams;
use Soluble\Jasper\ReportRunnerFactory;
use Soluble\Jasper\Runner\BridgedReportRunner;

class BridgedJasperReportRunnerTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    /**
     * @var Report
     */
    protected $report;

    public function setUp(): void
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
        $this->report        = new Report(\JasperTestsFactories::getDefaultReportFile());
    }

    public function testCompile(): void
    {
        $jasperRunner = new BridgedReportRunner($this->bridgeAdapter);
        $jasperReport = $jasperRunner->compileReport($this->report);
        self::assertInstanceOf(JasperReport::class, $jasperReport);
    }

    public function testCompileThrowsBrokenXmlException(): void
    {
        $reportFile = \JasperTestsFactories::getBrokenXMLReportFile();

        $report = new Report($reportFile);

        $this->expectException(BrokenXMLReportFileException::class);
        $this->expectExceptionMessageRegExp(
            sprintf(
                '#The report file "%s" cannot be parsed or not in jasper format#',
                $reportFile
            )
        );

        $jasperRunner = new BridgedReportRunner($this->bridgeAdapter);
        $jasperRunner->compileReport($report);
    }

    public function testFillWithInvalidJsonDataShouldThrowException(): void
    {
        $reportFile = \JasperTestsFactories::getReportBaseDir() . '/10_report_json_northwind.jrxml';
        $jsonFile   = \JasperTestsFactories::getDataBaseDir() . '/invalid_json.json';

        $this->expectException(BrokenJsonDataSourceException::class);
        $this->expectExceptionMessage(sprintf(
            'Fill error, json datasource cannot be parsed "%s" in %s',
            $jsonFile,
            $reportFile
        ));

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

        $reportRunner = ReportRunnerFactory::getBridgedReportRunner($this->bridgeAdapter);

        $report       = new Report($reportFile, $reportParams, $jsonDataSource);
        $jasperReport = $reportRunner->compileReport($report);
        $reportRunner->fillReport($jasperReport);
    }
}
