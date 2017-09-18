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

namespace JasperTest\Functionnal;

use Soluble\Jasper\DataSource\JsonDataSource;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportParams;
use Soluble\Jasper\ReportRunnerFactory;
use Soluble\Jasper\Runner\BridgedReportRunner;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Monolog\Logger;
use Monolog\Handler\TestHandler;

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

        $this->loggerName = '[soluble-jasper]';
        $this->logger = new Logger($this->loggerName);
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
            $logged = true;
            $logMsgs = $this->loggerTestHandler->getRecords() ?? [];
            self::assertCount(1, $logMsgs);
            $logMsg = $logMsgs[0]['message'] ?? '<nothing in the log>';
            self::assertContains('BrokenXMLReportFileException', $logMsg);
            self::assertContains('JasperCompileManager', $logMsg);
            self::assertContains(basename($reportFile), $logMsg);
        }
        if (!$logged) {
            self::assertFalse(true, sprintf(
                'Logger should log compilation error, found: %s',
                $this->loggerTestHandler->getRecords()[0]['message'] ?? '<nothing in the log>'
            ));
        }
    }

    public function testFillLoggingError(): void
    {
        $reportFile = \JasperTestsFactories::getReportBaseDir() . '/10_report_json_northwind.jrxml';
        $jsonFile = \JasperTestsFactories::getDataBaseDir() . '/invalid_json.json';

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
        $report = new Report($reportFile, $reportParams, $jsonDataSource);
        $jasperReport = $reportRunner->compileReport($report);

        try {
            $reportRunner->fillReport($jasperReport);
        } catch (\Exception $e) {
            $logged = true;
            $logMsgs = $this->loggerTestHandler->getRecords() ?? [];
            self::assertCount(1, $logMsgs);
            $logMsg = $logMsgs[0]['message'] ?? '<nothing in the log>';
            self::assertContains('BrokenJsonDataSourceException', $logMsg);
            self::assertContains('JasperFillManager', $logMsg);
            self::assertContains(basename($reportFile), $logMsg);
        }
        if (!$logged) {
            self::assertFalse(true, sprintf(
                'Logger should log filling error, found: %s',
                $this->loggerTestHandler->getRecords()[0]['message'] ?? '<nothing in the log>'
            ));
        }
    }
}
