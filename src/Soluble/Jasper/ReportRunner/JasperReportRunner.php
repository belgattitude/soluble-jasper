<?php

declare(strict_types=1);

namespace Soluble\Jasper\ReportRunner;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Report;
use Soluble\Jasper\Proxy\V6 as Proxy;
use Soluble\Jasper\Proxy\V6\CompiledJasperReport;
use Soluble\Jasper\ReportParams;

class JasperReportRunner implements ReportRunnerInterface
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    /**
     * @var Proxy\JasperCompileManager
     */
    protected $compileManager;

    /**
     * @var Proxy\JasperFillManager
     */
    protected $fillManager;

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
     * @return CompiledJasperReport
     */
    public function compileReport(Report $report): CompiledJasperReport
    {
        if ($this->compileManager === null) {
            $this->compileManager = new Proxy\JasperCompileManager($this->ba);
        }
        try {
            $compiledReport = $this->compileManager->compileReport($report->getReportFile());
        } catch (\Exception $e) {
            throw $e;
        }

        return new CompiledJasperReport($compiledReport, $report);
    }

    public function fillReport(CompiledJasperReport $compiledReport, ReportParams $reportParams = null, $datasource = null): Proxy\FilledJasperReport
    {
        if ($this->fillManager === null) {
            $this->fillManager = new Proxy\JasperFillManager($this->ba);
        }

        if ($datasource === null) {
            $datasource = new Proxy\JREmptyDataSource($this->ba);
        }

        $reportParams = $this->getReportParamsWithDefaults(
            $reportParams ?? new ReportParams(),
            $compiledReport->getReport()
        );

        $reportParamsMap = $this->buildReportParamsHashMap($reportParams);

        $filledReport = $this->fillManager->fillReport(
                                $compiledReport->getJavaProxiedObject(),
                                $reportParamsMap,
                                $datasource
        );

        return new Proxy\FilledJasperReport($filledReport);
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
    public function getReportParamsWithDefaults(ReportParams $reportParams, Report $report): ReportParams
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
