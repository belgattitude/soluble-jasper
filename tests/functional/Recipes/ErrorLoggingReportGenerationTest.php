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

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\DataSource\JsonDataSource;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportParams;
use Soluble\Jasper\ReportRunnerFactory;
use Soluble\Jasper\Runner\BridgedReportRunner;

class ErrorLoggingReportGenerationTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var TestHandler
     */
    protected $loggerTestHandler;

    /**
     * @var string
     */
    protected $loggerName;

    public function setUp(): void
    {
        $this->ba = \JasperTestsFactories::getJavaBridgeAdapter();

        $this->loggerName        = '[soluble-jasper]';
        $this->logger            = new Logger($this->loggerName);
        $this->loggerTestHandler = new TestHandler(Logger::DEBUG);
        $this->logger->pushHandler($this->loggerTestHandler);
    }

    public function testCompilationLoggingError(): void
    {
        $reportFile = \JasperTestsFactories::getBrokenXMLReportFile();

        $report = new Report($reportFile);

        $logged = false;

        try {
            $jasperRunner = new BridgedReportRunner($this->ba, $this->logger);
            $jasperRunner->compileReport($report);
        } catch (\Exception $e) {
            $logged  = true;
            $logMsgs = $this->loggerTestHandler->getRecords() ?? [];
            self::assertCount(1, $logMsgs);
            $logMsg = $logMsgs[0]['message'] ?? '<nothing in the log>';
            self::assertStringContainsString('BrokenXMLReportFileException', $logMsg);
            self::assertStringContainsString('JasperCompileManager', $logMsg);
            self::assertStringContainsString(basename($reportFile), $logMsg);
        }
        self::assertTrue($logged, sprintf(
            'Logger should log compilation error, found: %s',
            $this->loggerTestHandler->getRecords()[0]['message'] ?? '<nothing in the log>'
        ));
    }

    public function testFillLoggingError(): void
    {
        $reportFile = \JasperTestsFactories::getReportBaseDir() . '/10_report_json_northwind.jrxml';
        $jsonFile   = \JasperTestsFactories::getDataBaseDir() . '/invalid_json.json';

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

        $logged = false;

        $reportRunner = ReportRunnerFactory::getBridgedReportRunner($this->ba, $this->logger);
        $report       = new Report($reportFile, $reportParams, $jsonDataSource);
        $jasperReport = $reportRunner->compileReport($report);

        try {
            $reportRunner->fillReport($jasperReport);
            self::fail(sprintf(
                'Logger should log filling error, found: %s',
                $this->loggerTestHandler->getRecords()[0]['message'] ?? '<nothing in the log>'
            ));
        } catch (\Throwable $e) {
            $logMsgs = $this->loggerTestHandler->getRecords() ?? [];
            self::assertCount(1, $logMsgs);
            $logMsg = $logMsgs[0]['message'] ?? '<nothing in the log>';
            self::assertStringContainsString('BrokenJsonDataSourceException', $logMsg);
            self::assertStringContainsString('JasperFillManager', $logMsg);
            self::assertStringContainsString(basename($reportFile), $logMsg);
        }
    }
}
