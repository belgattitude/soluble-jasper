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

namespace Soluble\Jasper\Proxy\Engine\Util;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Proxy\Engine\DefaultJasperReportsContext;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;

class LocalJasperReportsContext implements RemoteJavaObjectProxyInterface
{
    /**
     * @var BridgeAdapter
     */
    private $ba;

    /**
     * @var JavaObject Java('net.sf.jasperreports.engine.util.LocalJasperReportsContext')
     */
    private $localContext;

    /**
     * @var JavaObject|null Java('net.sf.jasperreports.engine.JasperReportsContext')
     */
    private $parentContext;

    /**
     * Create a local context, if no parentContext is given, assume the DefaultJasperReportsContext.
     *
     * @param BridgeAdapter $bridgeAdapter
     * @param JavaObject    $parentContext Java('net.sf.jasperreports.engine.JasperReportsContext')
     */
    public function __construct(BridgeAdapter $bridgeAdapter, ?JavaObject $parentContext = null)
    {
        $this->ba            = $bridgeAdapter;
        $this->parentContext = $parentContext;
    }

    public function setFileResolver(JavaObject $fileResolver): void
    {
        $this->getJavaProxiedObject()->setFileResolver($fileResolver);
    }

    /**
     * @param JavaObject $classLoader Java('java.lang.ClassLoader')
     */
    public function setClassLoader(JavaObject $classLoader): void
    {
        $this->getJavaProxiedObject()->setClassLoader($classLoader);
    }

    public function removeProperty(string $name): void
    {
        $this->getJavaProxiedObject()->removeProperty($name);
    }

    public function setPropertiesMap(iterable $properties): void
    {
        $this->getJavaProxiedObject()->setPropertiesMap($properties);
    }

    /**
     * @param mixed $value
     */
    public function setProperty(string $name, $value): void
    {
        $this->getJavaProxiedObject()->setProperty($name, $value);
    }

    /**
     * @return mixed|null
     */
    public function getProperty(string $name)
    {
        return $this->getJavaProxiedObject()->getProperty($name);
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.util.JasperReportsContext')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        if ($this->localContext === null) {
            $this->localContext = $this->ba->java(
                'net.sf.jasperreports.engine.util.LocalJasperReportsContext',
                $this->parentContext ?? (new DefaultJasperReportsContext($this->ba))->getJavaProxiedObject()
            );
        }

        return $this->localContext;
    }
}
