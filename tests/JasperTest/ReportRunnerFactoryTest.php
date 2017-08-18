<?php

declare(strict_types=1);

namespace JasperTest;

use PHPUnit\Framework\TestCase;
use Soluble\Jasper\Exception\UnsupportedRunnerException;
use Soluble\Jasper\ReportRunner\ReportRunnerInterface;
use Soluble\Jasper\ReportRunnerFactory;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

class ReportRunnerFactoryTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    public function setUp()
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testInvoke()
    {
        $reportRunner = (new ReportRunnerFactory())($this->bridgeAdapter);
        $this->assertInstanceOf(ReportRunnerInterface::class, $reportRunner);

        $reportRunner = (new ReportRunnerFactory())->__invoke($this->bridgeAdapter);
        $this->assertInstanceOf(ReportRunnerInterface::class, $reportRunner);
    }

    public function testInvokeThrowsUnsupportedRunnerException()
    {
        $this->expectException(UnsupportedRunnerException::class);
        $this->expectExceptionMessage('Unsupported runner "InVALIDrUnner", must be in');
        (new ReportRunnerFactory())($this->bridgeAdapter, 'InVALIDrUnner');
    }
}
