<?php

declare(strict_types=1);

namespace Soluble\Jasper\ReportRunner;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Report;
use Soluble\Jasper\Proxy\V6 as Proxy;

class JasperReportRunner implements ReportRunnerInterface
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    public function __construct(BridgeAdapter $bridgeAdapter)
    {
        $this->ba = $bridgeAdapter;
    }

    /**
     * @param Report $report
     *
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperReport')
     */
    public function compileReport(Report $report): JavaObject
    {
        $compileManager = new Proxy\JasperCompileManager($this->ba);
        try {
            $compiledReport = $compileManager->compileReport($report->getReportFile());
        } catch (\Exception $e) {
            throw $e;
        }

        return $compiledReport;
    }
}
