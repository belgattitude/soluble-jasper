<?php

declare(strict_types=1);

namespace JasperTest\Proxy\V6;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Exception\BrokenXMLReportFileException;
use Soluble\Jasper\Exception\ReportFileNotFoundException;
use Soluble\Jasper\Proxy\V6\JasperCompileManager;
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

    public function testCompileWithMissingFileShouldThrowException()
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

    public function testCompileWithBrokenXmlFileShouldThrowException()
    {
        $reportFile = \JasperTestsFactories::getBrokenXMLReportFile();

        $this->expectException(BrokenXMLReportFileException::class);
        $this->expectExceptionMessageRegExp(
            sprintf(
                //'/The report file "%s" cannot be parsed./',
                '#The report file "%s" cannot be parsed#',
                $reportFile
            )
        );
        $compileManager = new JasperCompileManager($this->bridgeAdapter);
        $compileManager->compileReport($reportFile);
    }
}
