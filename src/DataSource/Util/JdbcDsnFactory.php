<?php

declare(strict_types=1);

namespace Soluble\Jasper\DataSource\Util;

use Soluble\Jasper\Exception\InvalidArgumentException;

class JdbcDsnFactory
{
    /**
     * Return a JDBC DSN formatted string from options.
     *
     * @param string   $driver        driver name  (mysql/oracle/postgres...)
     * @param string   $db            database name
     * @param string   $host          server ip or hostname (i.e localhost)
     * @param string   $user          username to connect
     * @param string   $password      password to connect
     * @param string[] $driverOptions extra options as an associative array (i.e serverTimezone...)
     *
     * @return string i.e: "jdbc:[driver]://localhost/[database]?user=[user]&password=[password]&serverTimezone=UTC";
     */
    public static function createDsn(
                                    string $driver,
                                    string $db,
                                    string $host,
                                    string $user,
                                    string $password,
                                    array $driverOptions = []
    ): string {
        $extras = '';
        if (!empty($driverOptions)) {
            $tmp = [];
            foreach ($driverOptions as $key => $value) {
                $tmp[] = urlencode($key) . '=' . urlencode($value);
            }
            $extras = '&' . implode('&', $tmp);
        }

        return "jdbc:$driver://$host/$db?user=$user&password=$password" . $extras;
    }

    /**
     * Return a JDBC DSN formatted string from options.
     *
     * @param string[]|array[] $params associative array with ['driver', 'db', 'host', 'user', 'password'] and optionally ['driverOptions'] as array
     *
     * @return string i.e: "jdbc:[driver]://localhost/[database]?user=[user]&password=[password]&serverTimezone=UTC";
     */
    public static function createDsnFromParams(array $params): string
    {
        $driver = $params['driver'] ?? null;
        if ($driver === null) {
            throw new InvalidArgumentException('Missing required "driver" option.');
        }

        $db = $params['db'] ?? null;
        if ($db === null) {
            throw new InvalidArgumentException('Missing required "db" option.');
        }

        $host = $params['host'] ?? null;
        if ($host === null) {
            throw new InvalidArgumentException('Missing required "host" option.');
        }

        // use and password can be optional for sqlite
        $user = $params['user'] ?? '';
        $password = $params['password'] ?? ''; // can be cleartext

        $driverOptions = $params['driverOptions'] ?? [];

        if (!is_array($driverOptions)) {
            throw new InvalidArgumentException('Invalid type, "driverOptions" must be an array.');
        }

        return self::createDsn($driver, $db, $host, $user, $password, $driverOptions);
    }
}
