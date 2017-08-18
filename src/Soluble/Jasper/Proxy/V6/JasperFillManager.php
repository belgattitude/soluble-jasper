<?php

declare(strict_types=1);

namespace Soluble\Jasper\Proxy\V6;

use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;

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

    public function fillReport($compiled, $params, JRDataSourceInterface $datasource)
    {
        return $this->jasperFillManager->fillReport($compiled, $params, $datasource->getJavaProxiedObject());
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperFillManager')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->jasperFillManager;
    }
}
