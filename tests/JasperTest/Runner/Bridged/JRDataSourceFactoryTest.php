<?php

declare(strict_types=1);

namespace JasperTest\Runner\Bridged;

use JasperTestsFactories;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\DataSource\DataSourceInterface;
use Soluble\Jasper\DataSource\JavaSqlConnection;
use Soluble\Jasper\Exception\UnsupportedDataSourceException;
use Soluble\Jasper\Runner\Bridged\JRDataSourceConnection;
use Soluble\Jasper\Runner\Bridged\JRDataSourceFactory;

class JRDataSourceFactoryTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    public function setUp()
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testFactoryWithJDBC()
    {
        $ds = (new JRDataSourceFactory($this->bridgeAdapter))->__invoke(
                new JavaSqlConnection(
                    JasperTestsFactories::getJdbcDsn(),
                'com.mysql.jdbc.Driver'
                )
        );

        $this->assertInstanceOf(JRDataSourceConnection::class, $ds);
    }

    public function testFactoryThrowsException()
    {
        $this->expectException(UnsupportedDataSourceException::class);

        $c = new class() implements DataSourceInterface {
        };

        $ds = (new JRDataSourceFactory($this->bridgeAdapter))->__invoke(
            $c
        );
    }
}
