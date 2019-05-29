<?php

declare(strict_types=1);

/*
 * Jasper report integration for PHP
 *
 * @link      https://github.com/belgattitude/soluble-jasper
 * @author    Vanvelthem Sébastien
 * @copyright Copyright (c) 2017-2019 Vanvelthem Sébastien
 * @license   MIT
 */

namespace JasperTest\Exception;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Bridge\Exception\JavaException;
use Soluble\Jasper\Exception\JavaProxiedException;
use Soluble\Jasper\Report;

class JavaProxiedExceptionTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    /**
     * @var Report
     */
    protected $report;

    public function setUp(): void
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testGetJavaException(): void
    {
        // Create a JavaException

        try {
            $this->bridgeAdapter->java('java.math.BigInteger', 'cool');
            self::fail('Error, this must throw an exception !');
        } catch (JavaException $e) {
            $pe  = new JavaProxiedException($e, 'coucou', 10);
            $msg = $pe->getMessage();
            self::assertStringContainsString('coucou', $msg);
            self::assertStringContainsString('java.lang.NumberFormatException', $msg);
            self::assertEquals(10, $pe->getCode());
            $je = $pe->getJavaException();
            self::assertEquals($pe->getJvmStackTrace(), $je->getStackTrace());
        }
    }
}
