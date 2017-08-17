<?php

declare(strict_types=1);

namespace Soluble\Jasper;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;

class ReportRunnerJapha
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
        $reportFile = $report->getReportFile();
        $compileManager = $this->ba->javaClass('net.sf.jasperreports.engine.JasperCompileManager');
        $compiledReport = $compileManager->compileReport($reportFile);

        return $compiledReport;
    }

    public function export()
    {
    }
}
