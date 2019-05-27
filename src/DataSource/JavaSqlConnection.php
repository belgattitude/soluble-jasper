<?php

declare(strict_types=1);

/*
 * Jasper report integration for PHP
 *
 * @link      https://github.com/belgattitude/soluble-jasper
 * @author    Vanvelthem Sébastien
 * @copyright Copyright (c) 2017-2019 Vanvelthem Sébastien
 * @license   MIT
 */

namespace Soluble\Jasper\DataSource;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Bridge\Exception\JavaException;
use Soluble\Japha\Db\DriverManager;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\DataSource\Contract\JavaSqlConnectionInterface;
use Soluble\Jasper\Exception\JavaProxiedException;

class JavaSqlConnection implements JavaSqlConnectionInterface
{
    /**
     * @var string
     */
    private $dsn;

    /**
     * @var string
     */
    private $driverClass;

    /**
     * JDBCDataSource constructor.
     *
     * @param string $jdbcDsn     JDBC DSN, i.e.: "jdbc:mysql://localhost/database?user=X&password=Y&serverTimezone=UTC";
     * @param string $driverClass Java driver class, i.e.: 'com.mysql.jdbc.Driver' (must be in classpath on the jvm side)
     */
    public function __construct(string $jdbcDsn, string $driverClass)
    {
        $this->dsn         = $jdbcDsn;
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
     * @return JavaObject Java('java.sql.Connection')
     *
     * @throws JavaProxiedException when the sql connection cannot be initialized
     */
    public function getJasperReportSqlConnection(BridgeAdapter $bridgeAdapter): JavaObject
    {
        try {
            $connection = (new DriverManager($bridgeAdapter))->createConnection(
                $this->getJdbcDsn(),
                $this->getDriverClass()
            );
        } catch (JavaException $e) {
            throw new JavaProxiedException(
                $e,
                sprintf(
                    'Error getting sql connection: %s',
                    $e->getMessage()
            )
            );
        }

        return $connection;
    }
}
