<?php

declare(strict_types=1);

namespace Soluble\Jasper\Engine;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;

class JasperCompileManagerProxy
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    /**
     * @var \Soluble\Japha\Interfaces\JavaClass
     */
    protected $compileManager;

    public function __construct(BridgeAdapter $bridgeAdapter)
    {
        $this->ba = $bridgeAdapter;
        $this->compileManager = $this->ba->javaClass('net.sf.jasperreports.engine.JasperCompileManager');
    }

    /**
     * @param string $reportFile
     *
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperReport')
     */
    public function compileReport(string $reportFile): JavaObject
    {
        $compiledReport = $this->compileManager->compileReport($reportFile);

        return $compiledReport;
    }
}
