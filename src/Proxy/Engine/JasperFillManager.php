<?php

declare(strict_types=1);

namespace Soluble\Jasper\Proxy\Engine;

use Soluble\Japha\Bridge\Exception\JavaException;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\DataSource\JsonDataSource;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;
use Soluble\Jasper\Exception;

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
     * @param JavaObject Java('net.sf.jasperreports.engine.JasperReport')
     * @param JavaObject Java('java.util.HashMap')
     * @param JavaObject|null $dataSource Java('net.sf.jasperreports.engine.JRDataSource')
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
            if ($dataSource === null) {
                $jasperPrint = $this->jasperFillManager->fillReport($jasperReport, $params);
            } else {
                $jasperPrint = $this->jasperFillManager->fillReport($jasperReport, $params, $dataSource);
            }
        } catch (JavaException $e) {
            $this->processFillJavaException($e, $jasperReport, $params, $reportFile);
            throw $e;
        } catch (\Exception $e) {
            throw new Exception\RuntimeException($e->getMessage());
        }

        return $jasperPrint;
    }

    /**
     * @throws Exception\BrokenJsonDataSourceException when the json datasource cannot be parsed
     * @throws Exception\JavaProxiedException          when filling the report has encountered a Java error
     */
    protected function processFillJavaException(
                            JavaException $e,
                            JavaObject $jasperReport,
                            JavaObject $params,
                            ?string $reportFile = null
    ): void {
        $className = $e->getJavaClassName();
        if ($className === 'net.sf.jasperreports.engine.JRException') {
            $cause = $e->getCause();
            if (stripos($cause, 'JsonParseException') !== false) {
                throw new Exception\BrokenJsonDataSourceException($e, sprintf(
                    'Fill error, json datasource cannot be parsed "%s" in %s',
                    (string) $params[JsonDataSource::PARAM_JSON_SOURCE],
                    $reportFile ?? $jasperReport->getName()
                ));
            }
        }

        throw new Exception\JavaProxiedException(
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
