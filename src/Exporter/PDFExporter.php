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

namespace Soluble\Jasper\Exporter;

use Psr\Http\Message\ResponseInterface;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Proxy\Engine\Export\JRPdfExporter;
use Soluble\Jasper\Proxy\Engine\JasperPrint;
use Soluble\Jasper\Proxy\Export\SimplePdfExporterConfiguration;
use Soluble\Jasper\Report;
use Soluble\Jasper\Runner\BridgedReportRunner;
use Zend\Diactoros\Response;

class PDFExporter
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
     * @var JasperPrint
     */
    private $jasperPrint;

    /**
     * @var BridgeAdapter
     */
    private $ba;

    /**
     * @var JRPdfExporter
     */
    private $exporter;

    public function __construct(BridgedReportRunner $runner, Report $report)
    {
        $this->runner = $runner;
        $this->report = $report;
        $this->ba = $runner->getBridgeAdapter();
        $this->exporter = new JRPdfExporter($this->ba);
    }

    /**
     * @param string   $outputFile
     * @param string[] $pdfConfig
     */
    public function saveFile(string $outputFile, array $pdfConfig = null): void
    {
        $jasperPrint = $this->getFilledReport();
        $this->exporter->setExporterInput($jasperPrint->getJavaProxiedObject());
        $this->exporter->setExporterOutput(new \SplFileInfo($outputFile));
        if ($pdfConfig !== null) {
            $simplePdfConfig = $this->getPdfExporterConfiguration($pdfConfig);
            $this->exporter->setConfiguration($simplePdfConfig->getJavaProxiedObject());
        }
        $this->exporter->exportReport();
    }

    /**
     * @param string[]|null $pdfConfig
     * @return ResponseInterface
     */
    public function getPsr7Response(array $pdfConfig = null): ResponseInterface
    {
        $response = new Response();

        return $response;
    }

    /**
     * @param string[] $config
     */
    private function getPdfExporterConfiguration(array $config): SimplePdfExporterConfiguration
    {
        $pdfConfig = new SimplePdfExporterConfiguration($this->ba);

        return $pdfConfig;
    }

    private function getFilledReport(): JasperPrint
    {
        if ($this->jasperPrint === null) {
            $jasperReport = $this->runner->compileReport($this->report);
            $this->jasperPrint = $this->runner->fillReport(
                                                    $jasperReport,
                                                    $this->report->getReportParams(),
                                                    $this->report->getDataSource()
            );
        }

        return $this->jasperPrint;
    }
}
