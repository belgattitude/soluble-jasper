<?php

declare(strict_types=1);

namespace Soluble\Jasper\Proxy\Engine\Util;

use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Proxy\Engine\DefaultJasperReportsContext;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

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
     * Create a local context, if no parentContext is given, assume the DefaultJasperReportsContext.
     *
     * @param BridgeAdapter $bridgeAdapter
     * @param JavaObject    $parentContext Java('net.sf.jasperreports.engine.JasperReportsContext')
     */
    public function __construct(BridgeAdapter $bridgeAdapter, ?JavaObject $parentContext = null)
    {
        $this->ba = $bridgeAdapter;
        $this->localContext = $this->ba->java(
            'net.sf.jasperreports.engine.util.LocalJasperReportsContext',
            $parentContext ?? (new DefaultJasperReportsContext($this->ba))->getJavaProxiedObject()
        );
    }

    public function setFileResolver(JavaObject $fileResolver): void
    {
        $this->localContext->setFileResolver($fileResolver);
    }

    /**
     * @param JavaObject $classLoader Java('java.lang.ClassLoader')
     */
    public function setClassLoader(JavaObject $classLoader): void
    {
        $this->localContext->setClassLoader($classLoader);
    }

    public function removeProperty(string $name): void
    {
        $this->localContext->removeProperty($name);
    }

    public function setPropertiesMap(iterable $properties): void
    {
        $this->localContext->setPropertiesMap($properties);
    }

    /**
     * @param mixed $value
     */
    public function setProperty(string $name, $value): void
    {
        $this->localContext->setProperty($name, $value);
    }

    /**
     * @return mixed|null
     */
    public function getProperty(string $name)
    {
        return $this->localContext->getProperty($name);
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.util.JasperReportsContext')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->localContext;
    }
}
