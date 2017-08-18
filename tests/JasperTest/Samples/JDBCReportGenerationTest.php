<?php

declare(strict_types=1);

namespace JasperTest\Samples;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

class JDBCReportGenerationTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    public function setUp()
    {
        if (\JasperTestsFactories::isJdbcTestsEnabled()) {
            $this->markTestSkipped(
                'Skipping JDBCReportGeneration tests, enable option in phpunit.xml '
            );
        }
        $this->ba = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testMysqlReport()
    {
        $this->assertTrue(true);
    }
}
