<?php

declare(strict_types=1);

namespace Soluble\Jasper\Proxy\Engine;

use Soluble\Japha\Interfaces\JavaClass;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

class DefaultJasperReportsContext implements RemoteJavaObjectProxyInterface
{
    /**
     * @var BridgeAdapter
     */
    private $ba;

    /**
     * @var JavaObject Java('net.sf.jasperreports.engine.DefaultJasperReportsContext')
     */
    private $defaultContext;

    /**
     * @var JavaClass Java('net.sf.jasperreports.engine.DefaultJasperReportsContext')
     */
    private $defaultContextClass;

    public function __construct(BridgeAdapter $bridgeAdapter)
    {
        $this->ba = $bridgeAdapter;
        $this->defaultContextClass = $this->ba->javaClass('net.sf.jasperreports.engine.DefaultJasperReportsContext');
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.DefaultJasperReportsContext')
     */
    public function getInstance(): JavaObject
    {
        if ($this->defaultContext === null) {
            $this->defaultContext = $this->defaultContextClass->getInstance();
        }

        return $this->defaultContextClass->getInstance();
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperPrint')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->getInstance();
    }
}