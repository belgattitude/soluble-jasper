<?php

declare(strict_types=1);

namespace JasperTest\DataSource\Util;

use PHPUnit\Framework\TestCase;
use Soluble\Jasper\DataSource\Util\JdbcDsnFactory;
use Soluble\Jasper\Exception\InvalidArgumentException;

class JdbcDsnFactoryTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testCreateDsn(): void
    {
        $dsn = JdbcDsnFactory::createDsn('mysql', 'db', 'host', 'user', 'password', []);
        self::assertEquals('jdbc:mysql://host/db?user=user&password=password', $dsn);
    }

    public function testCreateDsnDriverOptions(): void
    {
        $driverOptions = [
            'param1' => 'Hello',
            'param2' => 'éà&AA'
        ];

        $dsn = JdbcDsnFactory::createDsn('mysql', 'db', 'host', 'user', 'password', $driverOptions);

        $expected = 'jdbc:mysql://host/db?user=user&password=password&param1=Hello&param2=%C3%A9%C3%A0%26AA';
        self::assertEquals($expected, $dsn);
    }

    public function testCreateDsnFromParamsWithDriverOptions(): void
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
        self::assertEquals($expected, $dsn);
    }

    public function testCreateDsnFromParamsThrowsExceptionMissingDriver(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required "driver" option.');
        $params = [
            'db'   => 'my_db',
            'host' => 'localhost',
        ];

        JdbcDsnFactory::createDsnFromParams($params);
    }

    public function testCreateDsnFromParamsThrowsInvalidArgumentException(): void
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

    public function testCreateDsnFromParamsThrowsExceptionMissingDb(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required "db" option.');
        $params = [
            'driver' => 'mysql',
            'host'   => 'localhost',
        ];

        JdbcDsnFactory::createDsnFromParams($params);
    }

    public function testCreateDsnFromParamsThrowsExceptionMissingHost(): void
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
