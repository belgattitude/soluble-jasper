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
     * @param string $reportFile
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
        $jasperReport = null;

        try {
            $jasperReport = $this->compileManager->compileReport($reportFile);
        } catch (JavaException $e) {
            $this->processCompileJavaException($e, $reportFile);
        } catch (\Throwable $e) {
            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        return $jasperReport;
    }

    /**
     * @throws Exception\BrokenXMLReportFileException        when cannot parse the xml content or invalid xml file
     * @throws Exception\ReportFileNotFoundException         when the report file is not found
     * @throws Exception\ReportFileNotFoundFromJavaException when the report file is not found from the java side
     * @throws Exception\ReportCompileException              when there's an error compiling/evaluating the report
     * @throws Exception\JavaProxiedException                when the compileReport has encountered a Java error
     */
    protected function processCompileJavaException(JavaException $e, string $reportFile): void
    {
        $className = $e->getJavaClassName();
        if ($className === 'net.sf.jasperreports.engine.JRException') {
            $cause = $e->getCause();
            if (strpos($cause, 'java.io.FileNotFoundException') !== false) {
                if (file_exists($reportFile)) {
                    throw new Exception\ReportFileNotFoundFromJavaException(sprintf(
                        'Report file "%s" exists but cannot be located from the java side.',
                        $reportFile
                    ));
                } else {
                    throw new Exception\ReportFileNotFoundException(sprintf(
                        'Report file "%s" cannot be found',
                        $reportFile
                    ));
                }
            } elseif (strpos($cause, 'org.xml.sax.SAXParseException') !== false) {
                throw new Exception\BrokenXMLReportFileException($e, sprintf(
                    'The report file "%s" cannot be parsed or not in jasper format',
                    $reportFile
                ));
            } elseif (strpos($cause, 'Errors were encountered when compiling report expressions class file') !== false) {
                throw new Exception\ReportCompileException($e, sprintf(
                    'Report compilation failed for "%s"',
                    $reportFile
                ));
            }
        }
        throw new Exception\JavaProxiedException(
            $e,
            sprintf(
                'Error running report "%s"',
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
