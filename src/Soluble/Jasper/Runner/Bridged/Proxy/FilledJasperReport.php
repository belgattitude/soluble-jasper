<?php

declare(strict_types=1);

namespace Soluble\Jasper\Runner\Bridged\Proxy;

use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportInterface;
use Soluble\Jasper\Runner\Bridged\RemoteJavaObjectProxyInterface;

class FilledJasperReport implements RemoteJavaObjectProxyInterface, ReportInterface
{
    /**
     * @var JavaObject Java('net.sf.jasperreports.engine.JasperPrint')
     */
    protected $filledReport;

    /**
     * @var Report
     */
    protected $report;

    /**
     * @param JavaObject $filledReport Java('net.sf.jasperreports.engine.JasperPrint')
     */
    public function __construct(JavaObject $filledReport, Report $report)
    {
        $this->filledReport = $filledReport;
        $this->report = $report;
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperPrint')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->filledReport;
    }

    public function getStatus(): string
    {
        return ReportInterface::STATUS_FILLED;
    }
}
