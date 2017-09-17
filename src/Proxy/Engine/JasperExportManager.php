<?php

declare(strict_types=1);

namespace Soluble\Jasper\Proxy\Engine;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;

class JasperExportManager implements RemoteJavaObjectProxyInterface
{
    /**
     * @var BridgeAdapter
     */
    private $ba;

    /**
     * @var \Soluble\Japha\Interfaces\JavaClass Java(''net.sf.jasperreports.engine.JasperExportManager')
     */
    private $exportManager;

    public function __construct(BridgeAdapter $bridgeAdapter)
    {
        $this->ba = $bridgeAdapter;
        $this->exportManager = $this->ba->javaClass('net.sf.jasperreports.engine.JasperExportManager');
    }

    /**
     * @param JavaObject $jasperPrint Java('net.sf.jasperreports.engine.JasperPrint')
     */
    public function exportReportToPdfFile(JavaObject $jasperPrint, string $outputFile): void
    {
        $this->exportManager->exportReportToPdfFile($jasperPrint, $outputFile);
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperExportManager')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->exportManager;
    }
}
