<?php

declare(strict_types=1);

namespace JasperTest\Context;

use PHPUnit\Framework\TestCase;
use Soluble\Jasper\Context\DefaultFileResolver;
use Soluble\Jasper\Exception\InvalidArgumentException;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Report;

class DefaultFileResolverTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    private $ba;

    public function setUp(): void
    {
        $this->ba = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testGetClassLoaderThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        $classLoader = new DefaultFileResolver($this->ba);
        $classLoader->getFileResolver(['/path/does/no/exists']);
    }

    public function testGetFileResolver(): void
    {
        $ba = $this->ba;
        $fileResolver = new DefaultFileResolver($ba);

        $paths = [
            dirname(\JasperTestsFactories::getReportBaseDir() . '/01_report_default.jrxml')
        ];

        $fileResolver = $fileResolver->getFileResolver($paths);

        $this->assertEquals('net.sf.jasperreports.engine.util.SimpleFileResolver', $ba->getClassName($fileResolver));
    }

    public function testGetClassLoaderFromReport(): void
    {
        $ba = $this->ba;

        $file = \JasperTestsFactories::getReportBaseDir() . '/01_report_default.jrxml';
        $report = new Report($file);

        $fileResolver = new DefaultFileResolver($ba);

        $fileResolver = $fileResolver->getReportFileResolver($report);

        $this->assertEquals('net.sf.jasperreports.engine.util.SimpleFileResolver', $ba->getClassName($fileResolver));
    }
}
