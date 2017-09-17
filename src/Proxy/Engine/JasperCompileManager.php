<?php

declare(strict_types=1);

namespace Soluble\Jasper\Proxy\Engine;

use Soluble\Japha\Bridge\Exception\JavaException;
use Soluble\Jasper\Exception;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;

class JasperCompileManager implements RemoteJavaObjectProxyInterface
{
    /**
     * @var BridgeAdapter
     */
    private $ba;

    /**
     * @var \Soluble\Japha\Interfaces\JavaClass Java('net.sf.jasperreports.engine.JasperCompileManager')
     */
    private $compileManager;

    public function __construct(BridgeAdapter $bridgeAdapter)
    {
        $this->ba = $bridgeAdapter;
        $this->compileManager = $this->ba->javaClass('net.sf.jasperreports.engine.JasperCompileManager');
    }

    /**
     * Compile the jrxml report file in a blazing fast representation.
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
            return $this->compileManager->compileReport($reportFile);
        } catch (JavaException $e) {
            throw $this->getCompileManagerJavaException($e, $reportFile);
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
            if (strpos($cause, 'java.io.FileNotFoundException') !== false) {
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
            } elseif (strpos($cause, 'org.xml.sax.SAXParseException') !== false) {
                $exception = new Exception\BrokenXMLReportFileException($e, sprintf(
                    'The report file "%s" cannot be parsed or not in jasper format',
                    $reportFile
                ));
            } elseif (strpos($cause, 'Errors were encountered when compiling report expressions class file') !== false) {
                $exception = new Exception\ReportCompileException($e, sprintf(
                    'Report compilation failed for "%s"',
                    $reportFile
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
        return $this->compileManager;
    }
}
