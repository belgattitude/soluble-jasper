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

namespace JasperTest\Proxy\Export;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Proxy\Export\SimplePdfExporterConfiguration;

class SimplePdfExporterConfigurationTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $bridgeAdapter;

    /**
     * @var SimplePdfExporterConfiguration
     */
    protected $config;

    public function setUp(): void
    {
        $this->bridgeAdapter = \JasperTestsFactories::getJavaBridgeAdapter();
        $this->config        = new SimplePdfExporterConfiguration($this->bridgeAdapter);
    }

    public function testConfiguration(): void
    {
        $proxy = $this->config->getJavaProxiedObject();
        $this->config->setCompressed(true);
        $this->config->setEncrypted(true);
        $this->config->set128BitKey(true);
        $this->config->setMetadataCreator('creator');
        $this->config->setMetadataAuthor('author');
        $this->config->setMetadataKeywords('keywords');
        $this->config->setMetadataTitle('title');
        $this->config->setMetadataSubject('subject');
        $this->config->setUserPassword('user_password');
        $this->config->setOwnerPassword('owner_password');

        self::assertSame('creator', (string) $proxy->getMetadataCreator());
        self::assertSame('author', (string) $proxy->getMetadataAuthor());
        self::assertSame('subject', (string) $proxy->getMetadataSubject());
        self::assertSame('keywords', (string) $proxy->getMetadataKeywords());
        self::assertSame('title', (string) $proxy->getMetadataTitle());
        self::assertSame('user_password', (string) $proxy->getUserPassword());
        self::assertSame('owner_password', (string) $proxy->getOwnerPassword());
        self::assertTrue($this->bridgeAdapter->isTrue($proxy->isCompressed()));
        self::assertTrue($this->bridgeAdapter->isTrue($proxy->isEncrypted()));
        self::assertTrue($this->bridgeAdapter->isTrue($proxy->is128BitKey()));
    }

    public function testJavaProxiedObject(): void
    {
        $proxy = $this->config->getJavaProxiedObject();
        self::assertEquals(
            'net.sf.jasperreports.export.SimplePdfExporterConfiguration',
            $this->bridgeAdapter->getClassName($proxy)
        );
    }
}
