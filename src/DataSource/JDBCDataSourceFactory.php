<?php

declare(strict_types=1);

namespace Soluble\Jasper\DataSource;

use Soluble\Japha\Db\DriverManager;

class JDBCDataSourceFactory
{
    /**
     * Return a JDBC DSN formatted string from options.
     *
     * @param string $driverClass drive java class, i.e 'com.mysql.jdbc.Driver'
     * @param string $driver      driver name  (mysql/mariadb/oracle/postgres...)
     * @param string $db          database name
     * @param string $host        server ip or name
     * @param string $user        username to connect
     * @param string $password    password to connect
     * @param array  $options     extra options as an associative array
     */
    public function createDataSourceFromParams(
        string $driverClass,
        string $driver,
        string $db,
        string $host,
        string $user,
        string $password,
        array $options = []
    ): JDBCDataSource {
        $dsn = self::createDsnFromParams($driver, $db, $host, $user, $password, $options);

        return new JDBCDataSource($driverClass, $dsn);
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
     * @return string i.e: "jdbc:mysql://localhost/database?user=X&password=Y&serverTimezone=UTC";
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
