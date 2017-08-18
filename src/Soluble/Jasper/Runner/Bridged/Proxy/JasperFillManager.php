<?php

declare(strict_types=1);

namespace Soluble\Jasper\Runner\Bridged\Proxy;

use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Runner\Bridged\RemoteJavaObjectProxyInterface;

class JasperFillManager implements RemoteJavaObjectProxyInterface
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    /**
     * @var \Soluble\Japha\Interfaces\JavaObject
     */
    protected $jasperFillManager;

    public function __construct(BridgeAdapter $bridgeAdapter)
    {
        $this->ba = $bridgeAdapter;
        $this->jasperFillManager = $this->ba->java('net.sf.jasperreports.engine.JasperFillManager');
    }

    /**
     * @param JavaObject Java('net.sf.jasperreports.engine.JasperReport')
     * @param JavaObject Java('java.util.HashMap')
     * @param JRDataSourceInterface $dataSource
     *
     * @return mixed
     */
    public function fillReport(JavaObject $compiled, JavaObject $params, JRDataSourceInterface $dataSource)
    {
        return $this->jasperFillManager->fillReport($compiled, $params, $dataSource->getJavaProxiedObject());
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperFillManager')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->jasperFillManager;
    }
}
