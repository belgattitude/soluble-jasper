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

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Exception;
use Soluble\Jasper\Io\Exception\InvalidDirectoryException;
use Soluble\Jasper\Io\JvmFileUtils;
use Soluble\Jasper\Proxy\Engine\JasperExportManager;
use Soluble\Jasper\Proxy\Engine\JasperPrint;
use Soluble\Jasper\Report;
use Soluble\Jasper\Runner\BridgedReportRunner;

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
     * @var JasperPrint
     */
    private $jasperPrint;

    /**
     * @var BridgeAdapter
     */
    private $ba;

    /**
     * @var JasperExportManager
     */
    private $exportManager;

    /**
     * @var JvmFileUtils
     */
    private $jvmFileUtils;

    public function __construct(BridgedReportRunner $runner, Report $report)
    {
        $this->runner        = $runner;
        $this->report        = $report;
        $this->ba            = $runner->getBridgeAdapter();
        $this->exportManager = new JasperExportManager($this->ba);
        $this->jvmFileUtils  = new JvmFileUtils($this->ba);
    }

    /**
     * @throws Exception\BrokenXMLReportFileException When cannot parse the xml content or invalid xml file
     * @throws Exception\ReportFileNotFoundException  When the report file cannot be located (both php and java sides)
     * @throws Exception\ReportCompileException       When there's an error compiling/evaluating the report
     * @throws Exception\JavaProxiedException         When the compileReport has encountered a Java error
     * @throws Exception\JavaSaveProxiedException     When the report directory cannot be written from the JVM side
     */
    public function savePdf(string $outputFile): void
    {
        // Is writable ?

        $outputDirectory = dirname($outputFile);

        try {
            $canWrite = $this->jvmFileUtils->isDirectoryWritable($outputDirectory);
        } catch (InvalidDirectoryException $e) {
            throw new Exception\InvalidArgumentException($e->getMessage());
        } catch (\Throwable $e) {
            // Others
            throw $e;
        }

        if (!$canWrite) {
            throw new Exception\IOJavaPermissionException(
                sprintf("Cannot write into directory '%s', permissions error", $outputDirectory)
            );
        }

        $this->exportManager->exportReportToPdfFile($this->getFilledReport()->getJavaProxiedObject(), $outputFile);
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
