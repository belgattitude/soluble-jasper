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

namespace JasperTest\Context;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Context\DefaultFileResolver;
use Soluble\Jasper\Exception\InvalidArgumentException;
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

    public function testGetClassLoaderThrowsInvalidArgumentException(): void
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

        self::assertEquals('net.sf.jasperreports.engine.util.SimpleFileResolver', $ba->getClassName($fileResolver));
    }

    public function testGetClassLoaderFromReport(): void
    {
        $ba = $this->ba;

        $file = \JasperTestsFactories::getReportBaseDir() . '/01_report_default.jrxml';
        $report = new Report($file);

        $fileResolver = new DefaultFileResolver($ba);

        $fileResolver = $fileResolver->getReportFileResolver($report);

        self::assertEquals('net.sf.jasperreports.engine.util.SimpleFileResolver', $ba->getClassName($fileResolver));
    }
}
