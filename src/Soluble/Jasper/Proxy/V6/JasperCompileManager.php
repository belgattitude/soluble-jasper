<?php

declare(strict_types=1);

namespace Soluble\Jasper\Proxy\V6;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;

class JasperCompileManager implements RemoteJavaObjectProxyInterface
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    /**
     * @var \Soluble\Japha\Interfaces\JavaClass
     */
    protected $compileManager;

    public function __construct(BridgeAdapter $bridgeAdapter)
    {
        $this->ba = $bridgeAdapter;
        $this->compileManager = $this->ba->javaClass('net.sf.jasperreports.engine.JasperCompileManager');
    }

    /**
     * @param string $reportFile
     *
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperReport')
     */
    public function compileReport(string $reportFile): JavaObject
    {
        $compiledReport = $this->compileManager->compileReport($reportFile);

        return $compiledReport;
    }

    public function getJavaProxiedObject(): JavaObject
    {
        return $this->compileManager;
    }
}
