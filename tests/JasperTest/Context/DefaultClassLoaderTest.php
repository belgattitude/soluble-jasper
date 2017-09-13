<?php

declare(strict_types=1);

namespace JasperTest\Context;

use PHPUnit\Framework\TestCase;
use Soluble\Jasper\Context\DefaultClassLoader;
use Soluble\Jasper\Exception\InvalidArgumentException;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Report;

class DefaultClassLoaderTest extends TestCase
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
        $classLoader = new DefaultClassLoader($this->ba);
        $classLoader->getClassLoader(['/path/does/no/exists']);
    }

    public function testGetClassLoader(): void
    {
        $ba = $this->ba;
        $classLoader = new DefaultClassLoader($ba);

        $paths = [
            dirname(\JasperTestsFactories::getReportBaseDir() . '/01_report_default.jrxml')
        ];

        $javaClassLoader = $classLoader->getClassLoader($paths);

        $this->assertEquals('java.net.URLClassLoader', $ba->getClassName($javaClassLoader));

        $javaUrls = $javaClassLoader->getUrls();

        $this->assertContains($paths[0], (string) $javaUrls[0]);
        $this->assertContains('class [Ljava', $ba->getDriver()->inspect($javaUrls));
        $this->assertEquals('java.net.URL', $ba->getClassName($javaUrls[0]));
    }

    public function testGetClassLoaderFromReport(): void
    {
        $ba = $this->ba;

        $file = \JasperTestsFactories::getReportBaseDir() . '/01_report_default.jrxml';
        $report = new Report($file);

        $classLoader = new DefaultClassLoader($ba);

        $javaClassLoader = $classLoader->getReportClassLoader($report);

        $this->assertEquals('java.net.URLClassLoader', $ba->getClassName($javaClassLoader));

        $javaUrls = $javaClassLoader->getUrls();

        $this->assertContains(dirname($file), (string) $javaUrls[0]);
        $this->assertEquals('java.net.URL', $ba->getClassName($javaUrls[0]));
    }
}
