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

namespace Soluble\Jasper\Runner;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Context\DefaultClassLoader;
use Soluble\Jasper\Context\DefaultFileResolver;
use Soluble\Jasper\DataSource\Contract\DataSourceInterface;
use Soluble\Jasper\DataSource\Contract\JavaSqlConnectionInterface;
use Soluble\Jasper\DataSource\Contract\JRDataSourceFromDataSourceInterface;
use Soluble\Jasper\DataSource\Contract\JRDataSourceFromReportParamsInterface;
use Soluble\Jasper\DataSource\EmptyDataSource;
use Soluble\Jasper\Exception;
use Soluble\Jasper\Exporter\BridgedExportManager;
use Soluble\Jasper\Exporter\ExportManagerInterface;
use Soluble\Jasper\JRParameter;
use Soluble\Jasper\Proxy\Engine\JasperCompileManager;
use Soluble\Jasper\Proxy\Engine\JasperFillManager;
use Soluble\Jasper\Proxy\Engine\JasperPrint;
use Soluble\Jasper\Proxy\Engine\JasperReport;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportParams;

class BridgedReportRunner implements ReportRunnerInterface
{
    /**
     * @var BridgeAdapter
     */
    private $ba;

    /**
     * @var JasperCompileManager
     */
    private $compileManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(BridgeAdapter $bridgeAdapter, LoggerInterface $logger = null)
    {
        $this->ba = $bridgeAdapter;
        if ($logger === null) {
            $logger = new NullLogger();
        }
        $this->logger = $logger;
    }

    /**
     * @param Report $report
     *
     * @throws Exception\BrokenXMLReportFileException when cannot parse the xml content or invalid xml file
     * @throws Exception\ReportFileNotFoundException  when the report file cannot be located (both php and java sides)
     * @throws Exception\ReportCompileException       when there's an error compiling/evaluating the report
     * @throws Exception\JavaProxiedException         when the compileReport has encountered a Java error
     * @throws Exception\RuntimeException             when an unexpected problem have been encountered
     *
     * @return JasperReport
     */
    public function compileReport(Report $report): JasperReport
    {
        try {
            if ($this->compileManager === null) {
                $this->compileManager = new JasperCompileManager($this->ba);
            }
            $jasperReport = $this->compileManager->compileReport($report->getReportFile());
        } catch (\Throwable $e) {
            $this->logger->error(
                sprintf(
                "Compilation of report '%s' failed with '%s' (%s)",
                    basename($report->getReportFile()),
                    (new \ReflectionClass($e))->getShortName(),
                    $e->getMessage()
                )
            );
            throw $e;
        }

        return new JasperReport($this->ba, $jasperReport, $report);
    }

    /**
     * @param JasperReport             $jasperReport The compiled version of the jasper report
     * @param ReportParams|null        $reportParams if set will override/add to the Report->getReportParams()
     * @param DataSourceInterface|null $dataSource   if ste, will overrive report datasource
     *
     * @return JasperPrint
     *
     * @throws Exception\JavaProxiedException
     * @throws Exception\BrokenJsonDataSourceException
     */
    public function fillReport(
            JasperReport $jasperReport,
            ReportParams $reportParams = null,
            DataSourceInterface $dataSource = null
    ): JasperPrint {
        try {
            // Step 1: Get the fill manager
            $fillManager = new JasperFillManager($this->ba);

            // Step 2: get the datasource
            if ($dataSource === null) {
                $dataSource = $jasperReport->getReport()->getDataSource() ?? new EmptyDataSource();
            }

            // Step 2: Assigning reportParams

            $originalReportParams = $jasperReport->getReport()->getReportParams() ?? new ReportParams();
            $reportParams = $originalReportParams->withMergedParams($reportParams ?? new ReportParams());

            // Step 3: Getting some defaults

            $reportPath = $jasperReport->getReport()->getReportPath();
            $fileResolver = (new DefaultFileResolver($this->ba))->getFileResolver([$reportPath]);
            $classLoader = (new DefaultClassLoader($this->ba))->getClassLoader([$reportPath]);
            //$resourceBundle = (new DefaultResourceBundle($this->>ba))->getResourceBundle();
            $reportParams->addParams([
                JRParameter::REPORT_FILE_RESOLVER => $fileResolver,
                JRParameter::REPORT_CLASS_LOADER  => $classLoader
            ]);

            // Step 4: Assign parameters from datasource or set the JrDatasource

            $javaDataSource = null;
            if ($dataSource instanceof JRDataSourceFromReportParamsInterface) {
                $dataSource->assignDataSourceReportParams($reportParams);
            } elseif ($dataSource instanceof JRDataSourceFromDataSourceInterface) {
                $javaDataSource = $dataSource->getJRDataSource($this->ba);
            } elseif ($dataSource instanceof JavaSqlConnectionInterface) {
                $javaDataSource = $dataSource->getJasperReportSqlConnection($this->ba);
            }

            $paramsHashMap = $this->ba->java('java.util.HashMap', $reportParams->toArray());

            $jasperPrint = $fillManager->fillReport(
                $jasperReport->getJavaProxiedObject(),
                $paramsHashMap,
                $javaDataSource,
                $jasperReport->getReport()->getReportFile()
            );

            return new JasperPrint($jasperPrint, $jasperReport->getReport());
        } catch (\Throwable $e) {
            $this->logger->error(
                sprintf(
                    "Filling report '%s' failed with '%s' (%s)",
                    basename($jasperReport->getReport()->getReportFile()),
                    (new \ReflectionClass($e))->getShortName(),
                    $e->getMessage()
                )
            );
            throw $e;
        }
    }

    public function getExportManager(Report $report): ExportManagerInterface
    {
        return new BridgedExportManager($this, $report);
    }

    public function getBridgeAdapter(): BridgeAdapter
    {
        return $this->ba;
    }
}
