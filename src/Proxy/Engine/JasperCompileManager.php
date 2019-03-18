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

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Bridge\Exception\JavaException;
use Soluble\Japha\Interfaces\JavaClass;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Exception;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;

class JasperCompileManager implements RemoteJavaObjectProxyInterface
{
    /**
     * @var BridgeAdapter
     */
    private $ba;

    /**
     * @var JavaClass Java('net.sf.jasperreports.engine.JasperCompileManager')
     */
    private $compileManager;

    public function __construct(BridgeAdapter $bridgeAdapter)
    {
        $this->ba = $bridgeAdapter;
    }

    /**
     * Compile the jrxml report file in a faster binary format.
     *
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperReport')
     *
     * @throws Exception\BrokenXMLReportFileException when cannot parse the xml content or invalid xml file
     * @throws Exception\ReportFileNotFoundException  when the report file cannot be located (both php and java sides)
     * @throws Exception\ReportCompileException       when there's an error compiling/evaluating the report
     * @throws Exception\JavaProxiedException         when the compileReport has encountered a Java error
     * @throws Exception\RuntimeException             when an unexpected problem have been encountered
     */
    public function compileReport(string $reportFile): JavaObject
    {
        try {
            return $this->getJavaProxiedObject()->compileReport($reportFile);
        } catch (JavaException $e) {
            throw $this->getCompileManagerJavaException($e, $reportFile);
        } catch (\Throwable $e) {
            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Compile the jrxml report file in a file.
     *
     * @throws Exception\InvalidArgumentException     when the source and dest are the same file
     * @throws Exception\BrokenXMLReportFileException when cannot parse the xml content or invalid xml file
     * @throws Exception\ReportFileNotFoundException  when the report file cannot be located (both php and java sides)
     * @throws Exception\ReportCompileException       when there's an error compiling/evaluating the report
     * @throws Exception\JavaProxiedException         when the compileReport has encountered a Java error
     * @throws Exception\RuntimeException             when an unexpected problem have been encountered
     */
    public function compileReportToFile(string $sourceFile, string $destFile): void
    {
        if (!file_exists($sourceFile)) {
            throw new Exception\ReportFileNotFoundException(
                sprintf(
                    'Report file %s does not exists',
                    $sourceFile
                )
            );
        }

        if (basename($sourceFile) === basename($destFile)) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Source and destination file names must be different (source: %s, dest: %s)',
                    $sourceFile,
                    $destFile
                    )
            );
        }

        try {
            $this->getJavaProxiedObject()->compileReportToFile($sourceFile, $destFile);
        } catch (JavaException $e) {
            throw $this->getCompileManagerJavaException($e, $sourceFile);
        } catch (\Throwable $e) {
            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Return mapped exception from jasper compile manager java:.
     *
     * Exception\BrokenXMLReportFileException        when cannot parse the xml content or invalid xml file
     * Exception\ReportFileNotFoundException         when the report file is not found
     * Exception\ReportFileNotFoundFromJavaException when the report file is not found from the java side
     * Exception\ReportCompileException              when there's an error compiling/evaluating the report
     * Exception\JavaProxiedException                when the compileReport has encountered a Java error
     */
    private function getCompileManagerJavaException(JavaException $e, string $reportFile): Exception\ExceptionInterface
    {
        $exception = null;

        $className = $e->getJavaClassName();
        if ($className === 'net.sf.jasperreports.engine.JRException') {
            $cause = $e->getCause();
            if (mb_strpos($cause, 'java.io.FileNotFoundException') !== false) {
                if (file_exists($reportFile)) {
                    $exception = new Exception\ReportFileNotFoundFromJavaException(sprintf(
                        'Report file "%s" exists but cannot be located from the java side.',
                        $reportFile
                    ));
                } else {
                    $exception = new Exception\ReportFileNotFoundException(sprintf(
                        'Report file "%s" cannot be found',
                        $reportFile
                    ));
                }
            } elseif (mb_strpos($cause, 'org.xml.sax.SAXParseException') !== false) {
                $exception = new Exception\BrokenXMLReportFileException($e, sprintf(
                    'The report file "%s" cannot be parsed or not in jasper format',
                    $reportFile
                ));
            } elseif (mb_strpos($cause, 'Errors were encountered when compiling report expressions class file') !== false) {
                $exception = new Exception\ReportCompileException($e, sprintf(
                    'Report compilation failed for "%s"',
                    $reportFile
                ));
            } elseif (mb_strpos($cause, 'Error saving file:') !== false) {
                $exception = new Exception\JavaSaveProxiedException($e, sprintf(
                    'Cannot save file, %s',
                    $cause
                ));
            }
        }

        return $exception ?? new Exception\JavaProxiedException(
            $e,
            sprintf(
                    'Error compiling report "%s"',
                    $reportFile
                )
            );
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperCompileManager')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        if ($this->compileManager === null) {
            $this->compileManager = $this->ba->javaClass('net.sf.jasperreports.engine.JasperCompileManager');
        }

        return $this->compileManager;
    }
}
