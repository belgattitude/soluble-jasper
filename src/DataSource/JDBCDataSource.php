<?php

declare(strict_types=1);

namespace Soluble\Jasper\DataSource;

class JDBCDataSource implements DataSourceInterface
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
     * @param string $driverClass Java driver class, i.e.: 'com.mysql.jdbc.Driver' (must be in classpath)
     */
    public function __construct(string $jdbcDsn, string $driverClass)
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
}
