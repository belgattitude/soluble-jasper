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

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Proxy\Engine\JasperFillManager;
use Soluble\Jasper\Proxy\Engine\Util\LocalJasperReportsContext;

class JasperFillManagerTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    public function setUp(): void
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testGetJavaProxiedObject(): void
    {
        $fm = new JasperFillManager($this->bridgeAdapter);
        self::assertEquals(
            'net.sf.jasperreports.engine.JasperFillManager',
            $this->bridgeAdapter->getClassName($fm->getJavaProxiedObject())
        );
    }

    public function testConstructWithContext(): void
    {
        $context = new LocalJasperReportsContext($this->bridgeAdapter);
        $fm = new JasperFillManager($this->bridgeAdapter, $context->getJavaProxiedObject());
        self::assertEquals(
            'net.sf.jasperreports.engine.JasperFillManager',
            $this->bridgeAdapter->getClassName($fm->getJavaProxiedObject())
        );
    }
}
