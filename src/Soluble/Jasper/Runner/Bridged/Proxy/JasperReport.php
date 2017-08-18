<?php

declare(strict_types=1);

namespace Soluble\Jasper\Runner\Bridged\Proxy;

use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Runner\Bridged\RemoteJavaObjectProxyInterface;

class JasperReport implements RemoteJavaObjectProxyInterface
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    /**
     * @var JavaObject Java('net.sf.jasperreports.engine.JasperReport')
     */
    protected $jasperReport;

    public function __construct(BridgeAdapter $bridgeAdapter)
    {
        $this->ba = $bridgeAdapter;
        $this->jasperReport = $bridgeAdapter->java('net.sf.jasperreports.engine.JasperReport');
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperReport')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->jasperReport;
    }
}
