<?php

declare(strict_types=1);

namespace Soluble\Jasper\Proxy\Engine;

use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Report;
use Soluble\Jasper\Report\ReportInterface;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;

class JasperPrint implements RemoteJavaObjectProxyInterface, ReportInterface
{
    /**
     * @var JavaObject Java('net.sf.jasperreports.engine.JasperPrint')
     */
    private $jasperPrint;

    /**
     * @var Report
     */
    private $report;

    /**
     * @param JavaObject $jasperPrint Java('net.sf.jasperreports.engine.JasperPrint')
     */
    public function __construct(JavaObject $jasperPrint, Report $report)
    {
        $this->jasperPrint = $jasperPrint;
        $this->report = $report;
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperPrint')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->jasperPrint;
    }

    public function getStatus(): string
    {
        return ReportInterface::STATUS_FILLED;
    }
}
