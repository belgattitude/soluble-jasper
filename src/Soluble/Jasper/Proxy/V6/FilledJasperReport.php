<?php

declare(strict_types=1);

namespace Soluble\Jasper\Proxy\V6;

use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;
use Soluble\Jasper\Report;
use Soluble\Jasper\Report\ReportInterface;

class FilledJasperReport implements RemoteJavaObjectProxyInterface, ReportInterface
{
    /**
     * @var JavaObject Java('net.sf.jasperreports.engine.JasperReport')
     */
    protected $filledReport;

    /**
     * @var Report
     */
    protected $report;

    /**
     * @param JavaObject $compiledReport Java('net.sf.jasperreports.engine.JasperReport')
     */
    public function __construct(JavaObject $filledReport, Report $report)
    {
        $this->filledReport = $filledReport;
        $this->report = $report;
    }

    /**
     * Return original report.
     *
     * @return string
     */
    public function getReport(): Report
    {
        return $this->report;
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperReport')
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
