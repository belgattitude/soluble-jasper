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

namespace JasperTest\DataSource;

use PHPUnit\Framework\TestCase;
use Soluble\Jasper\DataSource\JavaSqlConnection;
use Soluble\Jasper\Exception\JavaProxiedException;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

class JavaSqlConnectionTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    public function setUp(): void
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testProperties(): void
    {
        $dsn = 'jdbc:mysql://host/db?user=user&password=password';
        $driver = 'com.mysql.jdbc.Driver';
        $ds = new JavaSqlConnection($dsn, $driver);
        self::assertEquals($dsn, $ds->getJdbcDsn());
        self::assertEquals($driver, $ds->getDriverClass());
    }

    public function testGetJasperReportSqlConnectionThrowsException(): void
    {
        $this->expectException(JavaProxiedException::class);

        $dsn = 'jdbc:mysql://host/db?user=notauser&password=notapassword';
        $driver = 'com.mysql.jdbc.Driver';
        $ds = new JavaSqlConnection($dsn, $driver);
        $ds->getJasperReportSqlConnection($this->bridgeAdapter);
    }
}
