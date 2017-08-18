<?php

declare(strict_types=1);

namespace Soluble\Jasper\DataSource;

use Soluble\Japha\Db\DriverManager;

class JDBCDataSource implements DataSourceInterface
{
    /**
     * @var string
     */
    protected $dsn;

    /**
     * @var string
     */
    protected $driverClass;

    public function __construct(string $jdbcDsn, string $driverClass = 'com.mysql.jdbc.Driver')
    {
        $this->dsn = $jdbcDsn;
        $this->driverClass = $driverClass;
    }

    public function getDriverClass(): string
    {
        return $this->driverClass;
    }

    public function getJdbcDsn(): string
    {
        return $this->dsn;
    }

    /**
     * Return a JDBC DSN formatted string from options.
     *
     * @param string $driver   driver name  (mysql/mariadb/oracle/postgres...)
     * @param string $db       database name
     * @param string $host     server ip or name
     * @param string $user     username to connect
     * @param string $password password to connect
     * @param array  $options  extra options as an associative array
     *
     * @return string
     */
    public static function createDsnFromParams(
        string $driver,
                                            string $db,
                                            string $host,
                                            string $user,
                                            string $password,
                                            array $options = []
    ): string {
        DriverManager::getJdbcDsn($driver, $db, $host, $user, $password, $options);
    }
}
