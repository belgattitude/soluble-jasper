<?php

declare(strict_types=1);

namespace Soluble\Jasper\Proxy\V6;

use Soluble\Japha\Bridge\Exception\JavaException;
use Soluble\Jasper\Exception;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;

class JasperCompileManager implements RemoteJavaObjectProxyInterface
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
     * Compile the jrxml report file in a blazing fast representation.
     *
     * @param string $reportFile
     *
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperReport')
     *
     * @throws Exception\BrokenXMLReportFileException when cannot parse the xml content or invalid xml file
     * @throws Exception\ReportFileNotFoundException  when the report file cannot be located (both php and java sides)
     * @throws JavaException                          when the compileReport has encountered a Java error
     * @throws Exception\RuntimeException             when an unexpected problem have been encountered
     */
    public function compileReport(string $reportFile): JavaObject
    {
        $compiledReport = null;

        try {
            $compiledReport = $this->compileManager->compileReport($reportFile);
        } catch (JavaException $e) {
            $this->processCompileJavaException($e, $reportFile);
        } catch (\Exception $e) {
            throw new Exception\RuntimeException($e->getMessage());
        }

        return $compiledReport;
    }

    /**
     * @throws Exception\BrokenXMLReportFileException when cannot parse the xml content or invalid xml file
     * @throws Exception\ReportFileNotFoundException  when the report file cannot be located (both php and java sides)
     * @throws JavaException                          when the compileReport has encountered a Java error
     * @throws Exception\RuntimeException             when an unexpected problem have been encountered
     */
    protected function processCompileJavaException(JavaException $e, string $reportFile): void
    {
        $className = $e->getJavaClassName();
        switch ($className) {
            case 'net.sf.jasperreports.engine.JRException':
                $cause = $e->getCause();
                if (strpos($cause, 'java.io.FileNotFoundException') !== false) {
                    $msg = (file_exists($reportFile)) ?
                        'Report file "%s" exists but cannot be located from the java (servlet) side.'
                        :
                        'Report file "%s" cannot be found';
                    throw new Exception\ReportFileNotFoundException(sprintf(
                        $msg,
                        $reportFile
                    ));
                } elseif (strpos($cause, 'org.xml.sax.SAXParseException') !== false) {
                    throw new Exception\BrokenXMLReportFileException(sprintf(
                        'The report file "%s" cannot be parsed or not in jasper format (XML error cause: %s)',
                        $reportFile,
                        $cause
                    ));
                } else {
                    throw $e;
                }
                break;
            default:
                throw $e;
        }
    }

    public function getJavaProxiedObject(): JavaObject
    {
        return $this->compileManager;
    }
}
