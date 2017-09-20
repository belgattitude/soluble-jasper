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

namespace JasperTest\Proxy\Export;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Proxy\Export\SimplePdfExporterConfiguration;

class SimplePdfExporterConfigurationTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    /**
     * @var SimplePdfExporterConfiguration
     */
    protected $config;

    public function setUp(): void
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
        $this->config = new SimplePdfExporterConfiguration($this->bridgeAdapter);
    }

    public function testJavaProxiedObject(): void
    {
        $proxy = $this->config->getJavaProxiedObject();
        self::assertEquals(
            'net.sf.jasperreports.export.SimplePdfExporterConfiguration',
            $this->bridgeAdapter->getClassName($proxy)
        );
    }
}
