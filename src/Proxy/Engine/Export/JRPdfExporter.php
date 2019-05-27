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

namespace Soluble\Jasper\Proxy\Engine\Export;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Proxy\Export\SimplePdfExporterConfiguration;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;
use SplFileInfo;

class JRPdfExporter implements RemoteJavaObjectProxyInterface
{
    /**
     * @var BridgeAdapter
     */
    private $ba;

    /**
     * @var JavaObject Java('net.sf.jasperreports.engine.export.JRPdfExporter')
     */
    private $exporter;

    /**
     * Create a local context, if no parentContext is given, assume the DefaultJasperReportsContext.
     *
     * @param BridgeAdapter $bridgeAdapter
     */
    public function __construct(BridgeAdapter $bridgeAdapter)
    {
        $this->ba       = $bridgeAdapter;
        $this->exporter = $this->ba->java(
            'net.sf.jasperreports.engine.export.JRPdfExporter'
        );
    }

    /**
     * @param JavaObject $jasperPrint Java('net.sf.jasperreports.engine.JasperPrint')
     */
    public function setExporterInput(JavaObject $jasperPrint): void
    {
        $exporterInput = $this->ba->java(
            'net.sf.jasperreports.export.SimpleExporterInput',
            $jasperPrint
        );
        $this->exporter->setExporterInput($exporterInput);
    }

    public function setExporterOutput(SplFileInfo $file): void
    {
        $exporterOutput = $this->ba->java(
            'net.sf.jasperreports.export.SimpleOutputStreamExporterOutput',
            $file->getPathname()
        );
        $this->exporter->setExporterOutput($exporterOutput);
    }

    /**
     * Set custom pdf configuration (metadata,...).
     */
    public function setConfiguration(SimplePdfExporterConfiguration $exportConfig): void
    {
        $this->exporter->setConfiguration($exportConfig->getJavaProxiedObject());
    }

    public function exportReport(): void
    {
        $this->exporter->exportReport();
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.export.JRPdfExporter')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->exporter;
    }
}
