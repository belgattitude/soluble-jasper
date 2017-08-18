<?php

declare(strict_types=1);

namespace JasperTest\Exception;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Exception\JavaException;
use Soluble\Jasper\Exception\JavaProxiedException;
use Soluble\Jasper\Report;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

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

    public function setUp()
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testGetJavaException()
    {
        // Create a JavaException

        try {
            $this->bridgeAdapter->java('java.math.BigInteger', 'cool');
            $this->assertTrue(false, 'Error, this must throw an exception !');
        } catch (JavaException $e) {
            $pe = new JavaProxiedException($e, 'coucou', 10);
            $msg = $pe->getMessage();
            $this->assertContains('coucou', $msg);
            $je = $pe->getJavaException();
            $this->assertInstanceOf(JavaException::class, $je);
        }
    }
}
