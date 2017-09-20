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

namespace JasperTest\Proxy\Engine\Export;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Proxy\Engine\Export\JRPdfExporter;

class JRPdfExporterTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    /**
     * @var JRPdfExporter
     */
    protected $exporter;

    public function setUp(): void
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
        $this->exporter      = new JRPdfExporter($this->bridgeAdapter);
    }

    public function testJavaProxiedObject(): void
    {
        $proxy = $this->exporter->getJavaProxiedObject();
        self::assertEquals(
            'net.sf.jasperreports.engine.export.JRPdfExporter',
            $this->bridgeAdapter->getClassName($proxy)
        );
    }
}
