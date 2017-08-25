<?php

declare(strict_types=1);

namespace Soluble\Jasper\Proxy\Engine;

use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Report;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;

class JasperReport implements RemoteJavaObjectProxyInterface
{
    /**
     * @var JavaObject Java('net.sf.jasperreports.engine.JasperReport')
     */
    private $jasperReport;

    /**
     * @var Report
     */
    private $report;

    /**
     * @param JavaObject $jasperReport Java('net.sf.jasperreports.engine.JasperReport')
     * @param Report     $report       original report
     */
    public function __construct(JavaObject $jasperReport, Report $report)
    {
        $this->jasperReport = $jasperReport;
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

    public function getProperty(string $name)
    {
        return $this->jasperReport->getProperty($name);
    }

    public function setProperty(string $name, $value): void
    {
        $this->jasperReport->setProperty($name, $value);
    }

    public function removeProperty(string $name): void
    {
        $this->jasperReport->removeProperty($name);
    }

    public function getPropertyNames(): array
    {
        return $this->jasperReport->getPropertyNames();
    }

    public function getResourceBundle(): ?string
    {
        return $this->jasperReport->getResourceBundle();
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperReport')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->jasperReport;
    }
}
