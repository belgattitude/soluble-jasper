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

namespace Soluble\Jasper\Proxy\Export;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Proxy\Engine\DefaultJasperReportsContext;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;

class SimplePdfExporterConfiguration implements RemoteJavaObjectProxyInterface
{
    /**
     * @var BridgeAdapter
     */
    private $ba;

    /**
     * @var JavaObject Java('net.sf.jasperreports.export.SimplePdfExporterConfiguration')
     */
    private $config;

    /**
     * Create a local context, if no parentContext is given, assume the DefaultJasperReportsContext.
     *
     * @param BridgeAdapter $bridgeAdapter
     */
    public function __construct(BridgeAdapter $bridgeAdapter)
    {
        $this->ba     = $bridgeAdapter;
        $this->config = $this->ba->java(
            'net.sf.jasperreports.export.SimplePdfExporterConfiguration'
        );
    }

    public function setCompressed(bool $compressed): void
    {
        $this->config->setCompressed($compressed);
    }

    public function setEncrypted(bool $encrypted): void
    {
        $this->config->setEncrypted($encrypted);
    }

    public function set128BitKey(bool $use128BitKey): void
    {
        $this->config->set128BitKey($use128BitKey);
    }

    public function setOwnerPassword(string $password): void
    {
        $this->config->setOwnerPassword($password);
    }

    public function setUserPassword(string $password): void
    {
        $this->config->setUserPassword($password);
    }

    public function setMetadataAuthor(string $author): void
    {
        $this->config->setMetadataAuthor($author);
    }

    public function setMetadataCreator(string $creator): void
    {
        $this->config->setMetadataCreator($creator);
    }

    public function setMetadataKeywords(string $keywords): void
    {
        $this->config->setMetadataKeywords($keywords);
    }

    public function setMetadataSubject(string $subject): void
    {
        $this->config->setMetadataSubject($subject);
    }

    public function setMetadataTitle(string $title): void
    {
        $this->config->setMetadataTitle($title);
    }

    public function setPdfVersion(string $version): void
    {
        $this->config->setPdfVersion($version);
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.export.SimplePdfExporterConfiguration')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->config;
    }
}
