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

namespace JasperTest\Proxy\Engine;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Exception\BrokenXMLReportFileException;
use Soluble\Jasper\Exception\InvalidArgumentException;
use Soluble\Jasper\Exception\ReportCompileException;
use Soluble\Jasper\Exception\ReportFileNotFoundException;
use Soluble\Jasper\Exception\ReportFileNotFoundFromJavaException;
use Soluble\Jasper\Proxy\Engine\JasperCompileManager;

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

    public function setUp(): void
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
        $directory           = [
            'reports' => [
                'report.jrxml' => '<xml></xml>'
            ]
        ];
        // setup and cache the virtual file system
        $this->vfs = vfsStream::setup('root', 444, $directory);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCompileShouldWork(): void
    {
        $reportFile     = \JasperTestsFactories::getDefaultReportFile();
        $compileManager = new JasperCompileManager($this->bridgeAdapter);
        $jasperReport   = $compileManager->compileReport($reportFile);
    }

    public function testCompileWithMissingFileShouldThrowReportNotFoundException(): void
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

    public function testCompileThrowReportNotFoundExceptionFromJava(): void
    {
        $this->expectException(ReportFileNotFoundFromJavaException::class);
        $this->expectExceptionMessage(sprintf(
            'Report file "%s" exists but cannot be located from the java side.',
            'vfs://root/reports/report.jrxml'
        ));

        $compileManager = new JasperCompileManager($this->bridgeAdapter);
        $compileManager->compileReport($this->vfs->url() . '/reports/report.jrxml');
    }

    public function testCompileWithBrokenXmlFileShouldThrowBrokenXMLException(): void
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

    public function testCompileWithNonJasperXmlFileShouldThrowException(): void
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

    public function testCompileWithExpressionErrorShouldThrowReportCompileException(): void
    {
        $reportFile = \JasperTestsFactories::getReportBaseDir() . '/04_report_expression_error.jrxml';

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

    public function testCompileToFileWithSameFilesShouldThrowInvalidArgumentException(): void
    {
        $srcFile = \JasperTestsFactories::getReportBaseDir() . '/04_report_expression_error.jrxml';
        $this->expectException(InvalidArgumentException::class);
        $compileManager = new JasperCompileManager($this->bridgeAdapter);
        $compileManager->compileReportToFile($srcFile, $srcFile);
    }

    public function testCompileToFileWithSameFilesShouldThrowReportFileNotFoundException(): void
    {
        $srcFile  = \JasperTestsFactories::getReportBaseDir() . '/non-existent-report.jrxml';
        $destFile = \JasperTestsFactories::getReportBaseDir() . '/non-existent-report-compiled.jasper';
        $this->expectException(ReportFileNotFoundException::class);
        $compileManager = new JasperCompileManager($this->bridgeAdapter);
        $compileManager->compileReportToFile($srcFile, $destFile);
    }

    public function testGetJavaProxiedObject(): void
    {
        $compileManager = new JasperCompileManager($this->bridgeAdapter);
        self::assertEquals(
            'net.sf.jasperreports.engine.JasperCompileManager',
            $this->bridgeAdapter->getClassName($compileManager->getJavaProxiedObject())
        );
    }
}
