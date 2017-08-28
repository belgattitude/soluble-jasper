<?php

declare(strict_types=1);

namespace JasperTest\Proxy\Engine;

use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Exception\BrokenXMLReportFileException;
use Soluble\Jasper\Exception\ReportCompileException;
use Soluble\Jasper\Exception\ReportFileNotFoundException;
use Soluble\Jasper\Exception\ReportFileNotFoundFromJavaException;
use Soluble\Jasper\Proxy\Engine\JasperCompileManager;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Report;
use org\bovigo\vfs\vfsStream;

class JasperCompileManagerTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    /**
     * @var vfsStreamDirectory
     */
    protected $vfs;

    public function setUp()
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
        $directory = [
            'reports' => [
                'report.jrxml' => '<xml></xml>'
            ]
        ];
        // setup and cache the virtual file system
        $this->vfs = vfsStream::setup('root', 444, $directory);
    }

    public function testCompileShouldWork()
    {
        $reportFile = \JasperTestsFactories::getDefaultReportFile();
        $compileManager = new JasperCompileManager($this->bridgeAdapter);
        $jasperReport = $compileManager->compileReport($reportFile);
        $this->assertInstanceOf(JavaObject::class, $jasperReport);
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

    public function testCompileThrowReportNotFoundExceptionFromJava()
    {
        $this->expectException(ReportFileNotFoundFromJavaException::class);
        $this->expectExceptionMessage(sprintf(
            'Report file "%s" exists but cannot be located from the java side.',
            'vfs://root/reports/report.jrxml'
        ));

        $compileManager = new JasperCompileManager($this->bridgeAdapter);
        $compileManager->compileReport($this->vfs->url() . '/reports/report.jrxml');
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
