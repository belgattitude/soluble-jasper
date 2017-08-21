<?php

declare(strict_types=1);

namespace Soluble\Jasper\Runner\Bridged\Proxy;

use Soluble\Japha\Bridge\Exception\JavaException;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Exception\JavaProxiedException;
use Soluble\Jasper\Runner\Bridged\RemoteJavaObjectProxyInterface;

class JasperFillManager implements RemoteJavaObjectProxyInterface
{
    /**
     * @var BridgeAdapter
     */
    private $ba;

    /**
     * @var \Soluble\Japha\Interfaces\JavaObject
     */
    private $jasperFillManager;

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
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperPrint')
     *
     * @throws JavaProxiedException
     */
    public function fillReport(JavaObject $compiled, JavaObject $params, JRDataSourceInterface $dataSource): JavaObject
    {
        try {
            $filledReport = $this->jasperFillManager->fillReport($compiled, $params, $dataSource->getJavaProxiedObject());
        } catch (JavaException $e) {
            throw new JavaProxiedException($e);
        }

        return $filledReport;
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperFillManager')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->jasperFillManager;
    }
}
