<?php

declare(strict_types=1);

namespace JasperTest\DataSource;

use PHPUnit\Framework\TestCase;
use Soluble\Jasper\DataSource\JdbcDataSource;

class JdbcDataSourceTest extends TestCase
{
    public function setUp()
    {
    }

    public function testProperties()
    {
        $dsn = 'jdbc:mysql://host/db?user=user&password=password';
        $driver = 'com.mysql.jdbc.Driver';
        $ds = new JdbcDataSource($dsn, $driver);
        $this->assertEquals($dsn, $ds->getJdbcDsn());
        $this->assertEquals($driver, $ds->getDriverClass());
    }
}
