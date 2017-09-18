<?php

declare(strict_types=1);

/*
 * Jasper report integration for PHP
 *
 * @link      https://github.com/belgattitude/soluble-jasper
 * @author    Vanvelthem Sébastien
 * @copyright Copyright (c) 2017 Vanvelthem Sébastien
 * @license   MIT
 */

namespace Soluble\Jasper\Proxy\Engine;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Bridge\Exception\JavaException;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\DataSource\JsonDataSource;
use Soluble\Jasper\Exception;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;

class JasperFillManager implements RemoteJavaObjectProxyInterface
{
    /**
     * @var BridgeAdapter
     */
    private $ba;

    /**
     * @var JavaObject Java('net.sf.jasperreports.engine.JasperFillManager')
     */
    private $jasperFillManager;

    public function __construct(BridgeAdapter $bridgeAdapter, JavaObject $jasperReportsContext = null)
    {
        $this->ba = $bridgeAdapter;
        if ($jasperReportsContext === null) {
            $this->jasperFillManager = $this->ba->javaClass('net.sf.jasperreports.engine.JasperFillManager');
        } else {
            $cls = $this->ba->javaClass('net.sf.jasperreports.engine.JasperFillManager');

            $this->jasperFillManager = $cls->getInstance($jasperReportsContext);
        }
    }

    /**
     * @param JavaObject      $jasperReport Java('net.sf.jasperreports.engine.JasperReport')
     * @param JavaObject      $params       Java('java.util.HashMap')
     * @param JavaObject|null $dataSource   Java('net.sf.jasperreports.engine.JRDataSource')
     *
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperPrint')
     *
     * @throws Exception\BrokenJsonDataSourceException When the json datasource cannot be parsed
     * @throws Exception\JavaProxiedException          When filling the report has encountered a Java error
     * @throws Exception\RuntimeException              An unexpected error happened
     */
    public function fillReport(
                            JavaObject $jasperReport,
                            JavaObject $params,
                            JavaObject $dataSource = null,
                            string $reportFile = null
    ): JavaObject {
        try {
            return ($dataSource === null) ?
                      $this->jasperFillManager->fillReport($jasperReport, $params)
                    : $this->jasperFillManager->fillReport($jasperReport, $params, $dataSource);
        } catch (JavaException $e) {
            throw $this->getFillManagerJavaException($e, $jasperReport, $params, $reportFile);
        } catch (\Throwable $e) {
            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Return jasperFillManager mapped exception:.
     *
     * Exception\BrokenJsonDataSourceException when the json datasource cannot be parsed
     * Exception\JavaProxiedException          when filling the report has encountered a Java error
     */
    private function getFillManagerJavaException(
                            JavaException $e,
                            JavaObject $jasperReport,
                            JavaObject $params,
                            ?string $reportFile = null
    ): Exception\ExceptionInterface {
        $exception = null;
        $className = $e->getJavaClassName();
        if ($className === 'net.sf.jasperreports.engine.JRException') {
            $cause = $e->getCause();
            if (mb_stripos($cause, 'JsonParseException') !== false) {
                $exception = new Exception\BrokenJsonDataSourceException($e, sprintf(
                    'Fill error, json datasource cannot be parsed "%s" in %s',
                    (string) $params[JsonDataSource::PARAM_JSON_SOURCE],
                    $reportFile ?? $jasperReport->getName()
                ));
            }
        }

        return $exception ?? new Exception\JavaProxiedException(
            $e,
            sprintf(
                'Error filling report "%s"',
                $reportFile ?? $jasperReport->getName()
            )
        );
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.JasperFillManager')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->jasperFillManager;
    }
}
