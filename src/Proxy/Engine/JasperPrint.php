<?php

declare(strict_types=1);

/*
 * Jasper report integration for PHP
 *
 * @link      https://github.com/belgattitude/soluble-jasper
 * @author    Vanvelthem Sébastien
 * @copyright Copyright (c) 2017 Vanvelthem Sébastien
 * @license   MIT
 */

namespace Soluble\Jasper\Proxy\Engine;

use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;
use Soluble\Jasper\Report;
use Soluble\Jasper\Report\ReportStatusInterface;

class JasperPrint implements RemoteJavaObjectProxyInterface, ReportStatusInterface
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
        return ReportStatusInterface::STATUS_FILLED;
    }
}
