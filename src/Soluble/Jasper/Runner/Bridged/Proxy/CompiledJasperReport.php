<?php

declare(strict_types=1);

namespace Soluble\Jasper\Runner\Bridged\Proxy;

use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportInterface;
use Soluble\Jasper\Runner\Bridged\RemoteJavaObjectProxyInterface;

class CompiledJasperReport implements RemoteJavaObjectProxyInterface, ReportInterface
{
    /**
     * @var JavaObject Java('net.sf.jasperreports.engine.JasperReport')
     */
    protected $compiledReport;

    /**
     * @var Report
     */
    protected $report;

    /**
     * @param JavaObject $compiledReport Java('net.sf.jasperreports.engine.JasperReport')
     * @param Report     $report         original report
     */
    public function __construct(JavaObject $compiledReport, Report $report)
    {
        $this->compiledReport = $compiledReport;
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

    public function getStatus(): string
    {
        return ReportInterface::STATUS_COMPILED;
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperReport')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->compiledReport;
    }
}