<?php

declare(strict_types=1);

namespace JasperTest\Runner\Bridged\Proxy;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Exception\BrokenXMLReportFileException;
use Soluble\Jasper\Exception\ReportCompileException;
use Soluble\Jasper\Exception\ReportFileNotFoundException;
use Soluble\Jasper\Runner\Bridged\Proxy\JasperCompileManager;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

class JasperCompileManagerTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    public function setUp()
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testCompileShouldWork()
    {
        $reportFile = \JasperTestsFactories::getDefaultReportFile();
        $compileManager = new JasperCompileManager($this->bridgeAdapter);
        $compiled = $compileManager->compileReport($reportFile);
        $this->assertInstanceOf(JavaObject::class, $compiled);
    }

    public function testCompileWithMissingFileShouldThrowReportNotFoundException()
    {
        $reportFile = '/tmp/invalid_file_not_exists.jrxml';

        $this->expectException(ReportFileNotFoundException::class);
        $this->expectExceptionMessage(sprintf(
           'Report file "%s" cannot be found',
            $reportFile
        ));

        $compileManager = new JasperCompileManager($this->bridgeAdapter);
        $compileManager->compileReport($reportFile);
    }

    public function testCompileWithBrokenXmlFileShouldThrowBrokenXMLException()
    {
        $reportFile = \JasperTestsFactories::getBrokenXMLReportFile();

        $this->expectException(BrokenXMLReportFileException::class);
        $this->expectExceptionMessageRegExp(
            sprintf(
                '#The report file "%s" cannot be parsed or not in jasper format#',
                $reportFile
            )
        );
        $compileManager = new JasperCompileManager($this->bridgeAdapter);
        $compileManager->compileReport($reportFile);
    }

    public function testCompileWithNonJasperXmlFileShouldThrowException()
    {
        $reportFile = \JasperTestsFactories::getNonJasperXMLReportFile();

        $this->expectException(BrokenXMLReportFileException::class);
        $this->expectExceptionMessageRegExp(
            sprintf(
                '#The report file "%s" cannot be parsed or not in jasper format#',
                $reportFile
            )
        );
        $compileManager = new JasperCompileManager($this->bridgeAdapter);
        $compileManager->compileReport($reportFile);
    }

    public function testCompileWithExpressionErrorShouldThrowReportCompileException()
    {
        $reportFile = \JasperTestsFactories::getReportBaseDir() . '/04_report_test_expression_error.jrxml';

        $this->expectException(ReportCompileException::class);
        $this->expectExceptionMessageRegExp(
            sprintf(
                '#Report compilation failed for "%s"#',
                $reportFile
            )
        );
        $compileManager = new JasperCompileManager($this->bridgeAdapter);
        $compileManager->compileReport($reportFile);
    }

    public function testGetJavaProxiedObject()
    {
        $compileManager = new JasperCompileManager($this->bridgeAdapter);
        $this->assertEquals(
            'net.sf.jasperreports.engine.JasperCompileManager',
            $this->bridgeAdapter->getClassName($compileManager->getJavaProxiedObject())
        );
    }
}
