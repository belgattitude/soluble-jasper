<?php

declare(strict_types=1);

namespace Soluble\Jasper\Exporter;

use Soluble\Jasper\Report;
use Soluble\Jasper\Runner\Bridged\Proxy\FilledJasperReport;
use Soluble\Jasper\Runner\Bridged\Proxy\JasperExportManager;
use Soluble\Jasper\Runner\Bridged\JRDataSourceFactory;
use Soluble\Jasper\Runner\Bridged\Proxy\JRDataSourceInterface;
use Soluble\Jasper\Runner\BridgedReportRunner;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Exception;

class BridgedExportManager implements ExportManagerInterface
{
    /**
     * @var BridgedReportRunner
     */
    private $runner;

    /**
     * @var Report
     */
    private $report;

    /**
     * @var FilledJasperReport
     */
    private $filledReport;

    /**
     * @var BridgeAdapter
     */
    private $ba;

    /**
     * @var JasperExportManager
     */
    private $exportManager;

    public function __construct(BridgedReportRunner $runner, Report $report)
    {
        $this->runner = $runner;
        $this->report = $report;
        $this->ba = $runner->getBridgeAdapter();
        $this->exportManager = new JasperExportManager($this->ba);
    }

    /**
     * @throws Exception\BrokenXMLReportFileException When cannot parse the xml content or invalid xml file
     * @throws Exception\ReportFileNotFoundException  When the report file cannot be located (both php and java sides)
     * @throws Exception\ReportCompileException       When there's an error compiling/evaluating the report
     * @throws Exception\JavaProxiedException         When the compileReport has encountered a Java error
     */
    public function savePdf(string $outputFile): void
    {
        $this->exportManager->exportReportToPdfFile($this->getFilledReport()->getJavaProxiedObject(), $outputFile);
    }

    protected function getFilledReport(): FilledJasperReport
    {
        if ($this->filledReport === null) {
            $compiledReport = $this->runner->compileReport($this->report);
            $this->filledReport = $this->runner->fillReport(
                                                    $compiledReport,
                                                    $this->report->getReportParams(),
                                                    $this->getJRDataSource()
            );
        }

        return $this->filledReport;
    }

    protected function getJRDataSource(): ?JRDataSourceInterface
    {
        if ($this->report->getDataSource() === null) {
            return null;
        }

        return (new JRDataSourceFactory($this->ba))($this->report->getDataSource());
    }
}
