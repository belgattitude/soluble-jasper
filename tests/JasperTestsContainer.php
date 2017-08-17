<?php

declare(strict_types=1);

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

class JasperTestsContainer
{
    public static function getJavaBridgeAdapter(): BridgeAdapter
    {
        $servlet_address = $_SERVER['JAVABRIDGE_URL'] ?? null;
        if ($servlet_address === null) {
            throw new \RuntimeException(sprintf(
                'Error: your phpunit config file does not inform about "JAVABRDIGE_URL"'
            ));
        }

        $adapter = new BridgeAdapter([
            'driver'          => 'Pjb62',
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
}
