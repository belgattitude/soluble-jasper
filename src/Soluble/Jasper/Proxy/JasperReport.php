<?php

declare(strict_types=1);

namespace Soluble\Jasper\Proxy;

use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

class JasperReport implements RemoteJavaObjectProxyInterface
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    /**
     * @var \Soluble\Japha\Interfaces\JavaObject
     */
    protected $jasperReport;

    public function __construct(BridgeAdapter $bridgeAdapter)
    {
        $this->ba = $bridgeAdapter;
        $this->jasperReport = $this->ba->java('net.sf.jasperreports.engine.JasperReport');
    }

    public function getJavaProxiedObject(): JavaObject
    {
        return $this->jasperReport;
    }
}
