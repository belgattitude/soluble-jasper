<?php

declare(strict_types=1);

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

class JasperTestsFactories
{
    const defaultReportTestFile = '01_report_test_default.jrxml';

    public static function getJavaBridgeAdapter(): BridgeAdapter
    {
        $servlet_address = $_SERVER['JAVABRIDGE_URL'] ?? null;
        if ($servlet_address === null) {
            throw new \RuntimeException(sprintf(
                'Error: your phpunit config file does not inform about "JAVABRDIGE_URL"'
            ));
        }

        $adapter = new BridgeAdapter([
            //'driver'          => 'Pjb62',
            'servlet_address' => $servlet_address
        ]);

        return $adapter;
    }

    /**
     * Return the report base directory where reports (jrxml) stands.
     *
     * @return string
     */
    public static function getReportBaseDir(): string
    {
        return __DIR__ . '/reports';
    }

    /**
     * Return the report output dir used for tests.
     *
     * @return string
     */
    public static function getOutputDir(): string
    {
        return __DIR__ . '/output';
    }

    public static function getDefaultReportFile(): string
    {
        return self::getReportBaseDir() . DIRECTORY_SEPARATOR . self::defaultReportTestFile;
    }

    public static function getBrokenXMLReportFile(): string
    {
        return self::getReportBaseDir() . DIRECTORY_SEPARATOR . '02_report_test_invalid_broken_xml.jrxml';
    }

    public static function getNonJasperXMLReportFile(): string
    {
        return self::getReportBaseDir() . DIRECTORY_SEPARATOR . '03_report_test_invalid_nonjasper_xml.jrxml';
    }

    public static function isJdbcTestsEnabled(): bool
    {
        return isset($_SERVER['ENABLE_MYSQL_JDBC_TESTS']) &&
            $_SERVER['ENABLE_MYSQL_JDBC_TESTS'] === 'true';
    }

    public static function getDatabaseConfig(): array
    {
        $mysql_config = [];
        $mysql_config['hostname'] = $_SERVER['MYSQL_HOSTNAME'];
        $mysql_config['username'] = $_SERVER['MYSQL_USERNAME'];
        $mysql_config['password'] = $_SERVER['MYSQL_PASSWORD'];
        $mysql_config['database'] = $_SERVER['MYSQL_DATABASE'];
        $mysql_config['driver_options'] = [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
        ];
        $mysql_config['options'] = [
            'buffer_results' => true
        ];
        $mysql_config['charset'] = 'UTF8';

        return $mysql_config;
    }

    public static function getJdbcDsn(): string
    {
        $config = self::getDatabaseConfig();
        $host = $config['hostname'];
        $db = $config['database'];
        $user = $config['username'];
        $password = $config['password'];
        $serverTimezone = urlencode('GMT+1');
        $dsn = "jdbc:mysql://$host/$db?user=$user&password=$password&serverTimezone=$serverTimezone";

        return $dsn;
    }
}
