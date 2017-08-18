<?php

declare(strict_types=1);

namespace Soluble\Jasper\Runner\Bridged\Proxy;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Runner\Bridged\RemoteJavaObjectProxyInterface;

class JasperExportManager implements RemoteJavaObjectProxyInterface
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    /**
     * @var \Soluble\Japha\Interfaces\JavaClass Java(''net.sf.jasperreports.engine.JasperExportManager')
     */
    protected $exportManager;

    public function __construct(BridgeAdapter $bridgeAdapter)
    {
        $this->ba = $bridgeAdapter;
        $this->exportManager = $this->ba->javaClass('net.sf.jasperreports.engine.JasperExportManager');
    }

    /*
    public function exportToPdfFile(string $reportFile, string $outputFile): void {

        $params = $this->ba->java('java.util.HashMap', ['cool' => 'test']);

        $jpath = $this->ba->java('java.io.File', dirname($reportFile));
        $url = $jpath->toUrl(); // Java.net.URL
        $urls = [$url];
        $classLoader = $this->ba->java('java.net.URLClassLoader', $urls);
        $params->put('REPORT_CLASS_LOADER', $classLoader);

        $defaultContext = $this->ba->javaClass('net.sf.jasperreports.engine.DefaultJasperReportsContext')
            ->getInstance();
        $context = $this->ba->javaClass('net.sf.jasperreports.engine.util.LocalJasperReportsContext')
            ->getLocalContext($defaultContext, $params);
        $context->setClassLoader($classLoader);

        $fileResolver = $this->ba->java('net.sf.jasperreports.engine.util.SimpleFileResolver',
        $jpath);
        $context->setFileResolver($fileResolver);

        $exportManager = $this->ba
                              ->javaClass('net.sf.jasperreports.engine.JasperExportManager')
            ->getInstance($context);
        $in = $this->ba->java('java.lang.String', $reportFile);
        $out = $this->ba->java('java.lang.String', $outputFile);

        $exportManager->exportToPdfFile($in, $out);
    }*/

    /**
     * @param JavaObject $filledReport Java('net.sf.jasperreports.engine.JasperPrint')
     * @param string     $outputFile
     */
    public function exportReportToPdfFile(JavaObject $filledReport, string $outputFile): void
    {
        $this->exportManager->exportReportToPdfFile($filledReport, $outputFile);
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperExportManager')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->exportManager;
    }
}
