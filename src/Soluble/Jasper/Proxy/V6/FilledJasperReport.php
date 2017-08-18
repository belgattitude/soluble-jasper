<?php

declare(strict_types=1);

namespace Soluble\Jasper\Proxy\V6;

use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;

class FilledJasperReport implements RemoteJavaObjectProxyInterface
{
    /**
     * @var JavaObject Java('net.sf.jasperreports.engine.JasperReport')
     */
    protected $filledReport;

    /**
     * @param JavaObject $compiledReport Java('net.sf.jasperreports.engine.JasperReport')
     */
    public function __construct(JavaObject $filledReport)
    {
        $this->filledReport = $filledReport;
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperReport')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->filledReport;
    }
}
