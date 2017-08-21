<?php

declare(strict_types=1);

namespace Soluble\Jasper\Runner;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Exporter\BridgedExportManager;
use Soluble\Jasper\Runner\Bridged\Proxy\CompiledJasperReport;
use Soluble\Jasper\Runner\Bridged\Proxy\FilledJasperReport;
use Soluble\Jasper\Runner\Bridged\Proxy\JasperCompileManager;
use Soluble\Jasper\Runner\Bridged\Proxy\JasperExportManager;
use Soluble\Jasper\Runner\Bridged\Proxy\JasperFillManager;
use Soluble\Jasper\Runner\Bridged\Proxy\JRDataSourceInterface;
use Soluble\Jasper\Runner\Bridged\Proxy\JREmptyDataSource;
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
     * @var JasperFillManager
     */
    private $fillManager;

    /**
     * @var JasperExportManager
     */
    private $exportManager;

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
     * @return CompiledJasperReport
     */
    public function compileReport(Report $report): CompiledJasperReport
    {
        if ($this->compileManager === null) {
            $this->compileManager = new JasperCompileManager($this->ba);
        }
        try {
            $compiledReport = $this->compileManager->compileReport($report->getReportFile());
        } catch (\Exception $e) {
            throw $e;
        }

        return new CompiledJasperReport($compiledReport, $report);
    }

    public function fillReport(CompiledJasperReport $compiledReport, ReportParams $reportParams = null, JRDataSourceInterface $dataSource = null): FilledJasperReport
    {
        if ($this->fillManager === null) {
            $this->fillManager = new JasperFillManager($this->ba);
        }

        if ($dataSource === null) {
            $dataSource = new JREmptyDataSource($this->ba);
        }

        $reportParams = $this->getReportParamsWithDefaults(
            $reportParams ?? new ReportParams(),
            $compiledReport->getReport()
        );

        $reportParamsMap = $this->buildReportParamsHashMap($reportParams);

        $filledReport = $this->fillManager->fillReport(
                                $compiledReport->getJavaProxiedObject(),
                                $reportParamsMap,
                                $dataSource
        );

        return new FilledJasperReport($filledReport, $compiledReport->getReport());
    }

    public function getExportManager(Report $report): BridgedExportManager
    {
        return new BridgedExportManager($this, $report);
    }

    public function getBridgeAdapter(): BridgeAdapter
    {
        return $this->ba;
    }

    /**
     * @param ReportParams $reportParams
     *
     * @return JavaObject Java('java.util.HashMap')
     */
    protected function buildReportParamsHashMap(ReportParams $reportParams): JavaObject
    {
        $paramsMap = $this->ba->java('java.util.HashMap', []);
        foreach ($reportParams as $name => $value) {
            // eventually change types here
            $paramsMap->put($name, $value);
        }

        return $paramsMap;
    }

    /**
     * @param ReportParams $reportParams
     * @param Report       $report
     *
     * @return ReportParams
     */
    protected function getReportParamsWithDefaults(ReportParams $reportParams, Report $report): ReportParams
    {
        // Class loader
        $newParams = new ReportParams($reportParams);

        if (!$newParams->offsetExists('REPORT_CLASS_LOADER')) {
            $jpath = $this->ba->java('java.io.File', dirname($report->getReportFile()));
            $url = $jpath->toUrl(); // Java.net.URL
            $urls = [$url];
            $classLoader = $this->ba->java('java.net.URLClassLoader', $urls);
            $newParams->put('REPORT_CLASS_LOADER', $classLoader);
        }

        return $newParams;

        // Setting the class loader for the resource bundle
        // Assuming they are in the same directory as
        // the report file.
        /*
        $report_resource_bundle = $report->getResourceBundle();
        if ($report_resource_bundle != '') {
            $ResourceBundle = new JavaClass('java.util.ResourceBundle');
            $rb = $ResourceBundle->getBundle($report_resource_bundle, $locale, $classLoader);
            $this->params->put('REPORT_RESOURCE_BUNDLE', $rb);
        }*/
    }
}
