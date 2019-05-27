<?php

declare(strict_types=1);

/*
 * Jasper report integration for PHP
 *
 * @link      https://github.com/belgattitude/soluble-jasper
 * @author    Vanvelthem Sébastien
 * @copyright Copyright (c) 2017-2019 Vanvelthem Sébastien
 * @license   MIT
 */

namespace Soluble\Jasper\Proxy\Engine;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;
use Soluble\Jasper\Report;

class JasperReport implements RemoteJavaObjectProxyInterface
{
    /**
     * @var BridgeAdapter
     */
    private $ba;

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
    public function __construct(BridgeAdapter $bridgeAdapter, JavaObject $jasperReport, Report $report)
    {
        $this->ba           = $bridgeAdapter;
        $this->jasperReport = $jasperReport;
        $this->report       = $report;
    }

    /**
     * Return original report.
     */
    public function getReport(): Report
    {
        return $this->report;
    }

    /**
     * @return mixed
     */
    public function getProperty(string $name)
    {
        return $this->jasperReport->getProperty($name);
    }

    /**
     * @param mixed $value
     */
    public function setProperty(string $name, $value): void
    {
        $this->jasperReport->setProperty($name, $value);
    }

    public function removeProperty(string $name): void
    {
        $this->jasperReport->removeProperty($name);
    }

    /**
     * @return string[]
     */
    public function getPropertyNames(): array
    {
        return $this->ba->values($this->jasperReport->getPropertyNames());
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
