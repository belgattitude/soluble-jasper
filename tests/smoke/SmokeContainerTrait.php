<?php

declare(strict_types=1);

namespace JasperTest\Smoke;

use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Zend\ServiceManager\ServiceManager;

trait SmokeContainerTrait
{
    public function getClient(): Client
    {
        return new Client([
            'base_uri' => sprintf('http://%s:%s', EXPRESSIVE_SERVER_HOST, EXPRESSIVE_SERVER_PORT),
            'timeout'  => 5,
        ]);
    }

    public function getContainer(): ContainerInterface
    {
        $config = require __DIR__ . '/../server/expressive/config/autoload/soluble-jasper.global.php';
        $container = new ServiceManager();
        $container->setService('config', $config);
        return $container;
    }

}
