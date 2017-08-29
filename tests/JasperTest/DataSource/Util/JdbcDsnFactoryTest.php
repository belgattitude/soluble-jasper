<?php

declare(strict_types=1);

namespace JasperTest\DataSource\Util;

use PHPUnit\Framework\TestCase;
use Soluble\Jasper\DataSource\Util\JdbcDsnFactory;
use Soluble\Jasper\Exception\InvalidArgumentException;

class JdbcDsnFactoryTest extends TestCase
{
    public function setUp()
    {
    }

    public function testCreateDsn()
    {
        $dsn = JdbcDsnFactory::createDsn('mysql', 'db', 'host', 'user', 'password', []);
        $this->assertEquals('jdbc:mysql://host/db?user=user&password=password', $dsn);
    }

    public function testCreateDsnDriverOptions()
    {
        $driverOptions = [
            'param1' => 'Hello',
            'param2' => 'éà&AA'
        ];

        $dsn = JdbcDsnFactory::createDsn('mysql', 'db', 'host', 'user', 'password', $driverOptions);

        $expected = 'jdbc:mysql://host/db?user=user&password=password&param1=Hello&param2=%C3%A9%C3%A0%26AA';
        $this->assertEquals($expected, $dsn);
    }

    public function testCreateDsnFromParamsWithDriverOptions()
    {
        $params = [
            'driver'        => 'mysql',
            'db'            => 'my_db',
            'host'          => 'localhost',
            'user'          => 'username',
            'password'      => 'password',
            'driverOptions' => [
                'param1' => 'Hello',
                'param2' => 'éà&AA'
            ]
        ];

        $dsn = JdbcDsnFactory::createDsnFromParams($params);

        $expected = 'jdbc:mysql://localhost/my_db?user=username&password=password&param1=Hello&param2=%C3%A9%C3%A0%26AA';
        $this->assertEquals($expected, $dsn);
    }

    public function testCreateDsnFromParamsThrowsExceptionMissingDriver()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required "driver" option.');
        $params = [
            'db'   => 'my_db',
            'host' => 'localhost',
        ];

        JdbcDsnFactory::createDsnFromParams($params);
    }

    public function testCreateDsnFromParamsThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid type, "driverOptions" must be an array.');
        $params = [
            'driver'        => 'mysql',
            'db'            => 'my_db',
            'host'          => 'localhost',
            'user'          => 'username',
            'password'      => 'password',
            'driverOptions' => 1
        ];

        JdbcDsnFactory::createDsnFromParams($params);
    }

    public function testCreateDsnFromParamsThrowsExceptionMissingDb()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required "db" option.');
        $params = [
            'driver' => 'mysql',
            'host'   => 'localhost',
        ];

        JdbcDsnFactory::createDsnFromParams($params);
    }

    public function testCreateDsnFromParamsThrowsExceptionMissingHost()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required "host" option.');
        $params = [
            'driver' => 'mysql',
            'db'     => 'database',
        ];
        JdbcDsnFactory::createDsnFromParams($params);
    }
}
