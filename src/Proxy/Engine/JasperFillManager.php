<?php

declare(strict_types=1);

namespace Soluble\Jasper\Proxy\Engine;

use Soluble\Japha\Bridge\Exception\JavaException;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Exception\JavaProxiedException;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;

class JasperFillManager implements RemoteJavaObjectProxyInterface
{
    /**
     * @var BridgeAdapter
     */
    private $ba;

    /**
     * @var JavaObject Java('net.sf.jasperreports.engine.JasperFillManager')
     */
    private $jasperFillManager;

    public function __construct(BridgeAdapter $bridgeAdapter, JavaObject $jasperReportsContext = null)
    {
        $this->ba = $bridgeAdapter;
        if ($jasperReportsContext === null) {
            $this->jasperFillManager = $this->ba->javaClass('net.sf.jasperreports.engine.JasperFillManager');
        } else {
            $cls = $this->ba->javaClass('net.sf.jasperreports.engine.JasperFillManager');

            $this->jasperFillManager = $cls->getInstance($jasperReportsContext);
        }
    }

    /**
     * @param JavaObject Java('net.sf.jasperreports.engine.JasperReport')
     * @param JavaObject Java('java.util.HashMap')
     * @param JavaObject|null $dataSource Java('net.sf.jasperreports.engine.JRDataSource')
     *
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperPrint')
     *
     * @throws JavaProxiedException
     */
    public function fillReport(JavaObject $jasperReport, JavaObject $params, ?JavaObject $dataSource = null): JavaObject
    {
        try {
            if ($dataSource === null) {
                $jasperPrint = $this->jasperFillManager->fillReport($jasperReport, $params);
            } else {
                $jasperPrint = $this->jasperFillManager->fillReport($jasperReport, $params, $dataSource);
            }
        } catch (JavaException $e) {
            throw new JavaProxiedException($e);
        }

        return $jasperPrint;
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperFillManager')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->jasperFillManager;
    }
}
