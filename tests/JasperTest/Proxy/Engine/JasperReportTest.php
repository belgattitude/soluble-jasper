<?php

declare(strict_types=1);

namespace JasperTest\Proxy\Engine;

use JasperTestsFactories;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportRunnerFactory;

class JasperReportTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    public function setUp(): void
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testGetResourceBundle(): void
    {
        $report = new Report(JasperTestsFactories::getDefaultReportFile());

        $reportRunner = ReportRunnerFactory::getBridgedReportRunner($this->bridgeAdapter);
        $jasperReport = $reportRunner->compileReport($report);

        $this->assertNull($jasperReport->getResourceBundle());
    }

    public function testProperties(): void
    {
        $report = new Report(JasperTestsFactories::getDefaultReportFile());
        $reportRunner = ReportRunnerFactory::getBridgedReportRunner($this->bridgeAdapter);
        $jasperReport = $reportRunner->compileReport($report);
        $jasperReport->setProperty('COOL', 'test');
        $this->assertEquals('test', $jasperReport->getJavaProxiedObject()->getProperty('COOL'));
        $this->assertEquals('test', $jasperReport->getProperty('COOL'));

        $this->assertTrue(in_array('COOL', $jasperReport->getPropertyNames()));

        $jasperReport->removeProperty('COOL');

        $this->assertFalse(in_array('COOL', $jasperReport->getPropertyNames()));
    }
}
