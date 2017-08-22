<?php

declare(strict_types=1);

namespace Soluble\Jasper\Runner;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Exporter\BridgedExportManager;
use Soluble\Jasper\JRParameter;
use Soluble\Jasper\ReportProperties;
use Soluble\Jasper\Proxy\Engine\JasperReport;
use Soluble\Jasper\Proxy\Engine\JasperPrint;
use Soluble\Jasper\Proxy\Engine\JasperCompileManager;
use Soluble\Jasper\Proxy\Engine\JasperFillManager;
use Soluble\Jasper\Proxy\Engine\JRDataSourceInterface;
use Soluble\Jasper\Proxy\Engine\JREmptyDataSource;
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

        return new JasperReport($jasperReport, $report);
    }

    public function getReportContext()
    {
    }

    /**
     * @param JasperReport               $jasperReport     The compiled version of the jasper report
     * @param ReportParams|null          $reportParams
     * @param JRDataSourceInterface|null $dataSource
     * @param ReportProperties           $reportProperties
     *
     * @return JasperPrint
     *
     * @throws Exception\JavaProxiedException
     */
    public function fillReport(
        JasperReport $jasperReport,
                                ReportParams $reportParams = null,
                                JRDataSourceInterface $dataSource = null,
                                ReportProperties $reportProperties = null
    ): JasperPrint {
        // SetContext
        $defaultContext = $this->ba->javaClass('net.sf.jasperreports.engine.DefaultJasperReportsContext')->getInstance();
        $context = $this->ba->java('net.sf.jasperreports.engine.util.LocalJasperReportsContext', $defaultContext);

        $reportPath = $this->ba->java('java.io.File', dirname($jasperReport->getReport()->getReportFile()));
        $fileResolver = $this->ba->java('net.sf.jasperreports.engine.util.SimpleFileResolver', [
                $reportPath
            ]);

        $fileResolver->setResolveAbsolutePath(true);
        $context->setFileResolver($fileResolver);

        $urlReportPath = $this->ba->java('java.io.File', $reportPath)->toUrl();

        $classLoader = $this->ba->java('java.net.URLClassLoader', [
            $urlReportPath
        ]);
        //$newParams->put(JRParameter::REPORT_CLASS_LOADER, $classLoader);
        $context->setClassLoader($classLoader);

        $fillManager = new JasperFillManager($this->ba, $context);

        if ($dataSource === null) {
            $dataSource = new JREmptyDataSource($this->ba);
        }

        if ($reportProperties !== null) {
            foreach ($reportProperties as $name => $value) {
                $jasperReport->setProperty($name, $value);
            }
        }

        $reportParams = $this->getReportParamsWithDefaults(
            $reportParams ?? new ReportParams(),
            $jasperReport->getReport()
        );

        $reportParamsMap = $this->buildReportParamsHashMap($reportParams);
        $reportParamsMap->put(JRParameter::REPORT_FILE_RESOLVER, $fileResolver);
        $reportParamsMap->put(JRParameter::REPORT_CLASS_LOADER, $classLoader);

        $jasperPrint = $fillManager->fillReport(
                                $jasperReport->getJavaProxiedObject(),
                                $reportParamsMap,
                                $dataSource
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

        /*
                if (!$newParams->offsetExists(JRParameter::REPORT_CLASS_LOADER)) {
                    $jpath = $this->ba->java('java.io.File', dirname($report->getReportFile()));
                    $url = $jpath->toUrl(); // Java.net.URL
                    $urls = [$url];
                    $classLoader = $this->ba->java('java.net.URLClassLoader', $urls);
                    $newParams->put(JRParameter::REPORT_CLASS_LOADER, $classLoader);
                }
        */

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
