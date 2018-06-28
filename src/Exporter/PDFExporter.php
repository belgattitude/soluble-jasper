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
use Soluble\Japha\Bridge\Exception\JavaException;
use Soluble\Jasper\Exception\IOException;
use Soluble\Jasper\Exception\IOPermissionException;
use Soluble\Jasper\Proxy\Engine\Export\JRPdfExporter;
use Soluble\Jasper\Proxy\Engine\JasperPrint;
use Soluble\Jasper\Proxy\Export\SimplePdfExporterConfiguration;
use Soluble\Jasper\Report;
use Soluble\Jasper\Runner\BridgedReportRunner;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

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
     * @var JRPdfExporter
     */
    private $exporter;

    public function __construct(Report $report, BridgedReportRunner $runner)
    {
        $this->runner   = $runner;
        $this->report   = $report;
        $this->exporter = new JRPdfExporter($runner->getBridgeAdapter());
    }

    /**
     * @param string   $outputFile
     * @param string[] $pdfConfig
     *
     * @throws JavaException
     */
    public function saveFile(string $outputFile, array $pdfConfig = null): void
    {
        $jasperPrint = $this->getFilledReport();
        $this->exporter->setExporterInput($jasperPrint->getJavaProxiedObject());
        $this->exporter->setExporterOutput(new \SplFileInfo($outputFile));
        if ($pdfConfig !== null) {
            $simplePdfConfig = $this->getPdfExporterConfiguration($pdfConfig);
            $this->exporter->setConfiguration($simplePdfConfig);
        }
        $this->exporter->exportReport();
    }

    /**
     * Return a new PSR-7 Response object filled with the PDF content.
     *
     * @param string[]|null     $pdfConfig
     * @param ResponseInterface $response  initial response
     *
     * @throws IOException
     * @throws IOPermissionException
     * @throws JavaException
     */
    public function getPsr7Response(array $pdfConfig = null, ResponseInterface $response=null): ResponseInterface
    {
        $tmpFile = $this->createTempFile();

        try {
            $this->saveFile($tmpFile, $pdfConfig);
        } catch (\Throwable $e) {
            if (file_exists($tmpFile)) {
                unlink($tmpFile);
            }
            throw $e;
        }

        if ($response === null) {
            $response = new Response();
        }

        $response = $response->withBody(new Stream($tmpFile));
        $response = $response->withHeader('Content-type', 'application/pdf');

        if (@unlink($tmpFile) === false) {
            $this->runner->getLogger()->warning(
                sprintf(
                    "Could not delete temp file '%s' after PSR7 response generation: %s.",
                    $tmpFile,
                    file_exists($tmpFile) ? 'File exists, cannot unlink' : 'File does not exists'
                )
            );
        }

        return $response;
    }

    /**
     * @param null|string $tmpDir if null use sys_get_temp_dir()
     * @param int         $mode   default to '0666'
     *
     * @throws IOException
     * @throws IOPermissionException
     */
    protected function createTempFile(?string $tmpDir=null, int $mode=0666): string
    {
        $tmpDir  = $tmpDir ?? sys_get_temp_dir();
        $tmpFile = tempnam($tmpDir, 'soluble-jasper');
        if ($tmpFile === false) {
            throw new IOException(sprintf(
                'Cannot create temporary file in %s',
                $tmpDir
            ));
        }
        if (chmod($tmpFile, $mode) === false) {
            unlink($tmpFile);
            throw new IOPermissionException(sprintf(
                'Cannot set file permission of file %s.',
                $tmpFile
            ));
        }

        return $tmpFile;
    }

    private function getPdfExporterConfiguration(array $config): SimplePdfExporterConfiguration
    {
        $pdfConfig = new SimplePdfExporterConfiguration($this->runner->getBridgeAdapter());

        return $pdfConfig;
    }

    private function getFilledReport(): JasperPrint
    {
        if ($this->jasperPrint === null) {
            $jasperReport      = $this->runner->compileReport($this->report);
            $this->jasperPrint = $this->runner->fillReport(
                                                    $jasperReport,
                                                    $this->report->getReportParams(),
                                                    $this->report->getDataSource()
            );
        }

        return $this->jasperPrint;
    }
}
