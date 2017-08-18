<?php

declare(strict_types=1);

namespace Soluble\Jasper\Proxy\V6;

use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;

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

    /**
     * @param JavaObject $filledReport
     * @param string     $outputFile
     */
    public function exportReportToPdfFile(JavaObject $filledReport, string $outputFile)
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
