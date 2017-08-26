<?php

declare(strict_types=1);

namespace Soluble\Jasper\Runner;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Context\DefaultClassLoader;
use Soluble\Jasper\Context\DefaultFileResolver;
use Soluble\Jasper\DataSource\Contract\DataSourceInterface;
use Soluble\Jasper\DataSource\Contract\JavaSqlConnectionInterface;
use Soluble\Jasper\DataSource\Contract\JRDataSourceFromDataSourceInterface;
use Soluble\Jasper\DataSource\Contract\JRDataSourceFromReportParamsInterface;
use Soluble\Jasper\DataSource\EmptyDataSource;
use Soluble\Jasper\Exporter\BridgedExportManager;
use Soluble\Jasper\JRParameter;
use Soluble\Jasper\Proxy\Engine\JasperReport;
use Soluble\Jasper\Proxy\Engine\JasperPrint;
use Soluble\Jasper\Proxy\Engine\JasperCompileManager;
use Soluble\Jasper\Proxy\Engine\JasperFillManager;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportParams;
use Soluble\Jasper\Exception;

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
     * JasperReportRunner constructor.
     *
     * @param BridgeAdapter $bridgeAdapter
     */
    public function __construct(BridgeAdapter $bridgeAdapter)
    {
        $this->ba = $bridgeAdapter;
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
        if ($this->compileManager === null) {
            $this->compileManager = new JasperCompileManager($this->ba);
        }
        try {
            $jasperReport = $this->compileManager->compileReport($report->getReportFile());
        } catch (\Exception $e) {
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
     */
    public function fillReport(
            JasperReport $jasperReport,
            ReportParams $reportParams = null,
            DataSourceInterface $dataSource = null
    ): JasperPrint {
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
            $javaDataSource
        );

        return new JasperPrint($jasperPrint, $jasperReport->getReport());
    }

    public function getExportManager(Report $report): BridgedExportManager
    {
        return new BridgedExportManager($this, $report);
    }

    public function getBridgeAdapter(): BridgeAdapter
    {
        return $this->ba;
    }
}
