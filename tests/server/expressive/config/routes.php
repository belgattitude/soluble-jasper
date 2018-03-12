<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\XmlResponse;
use Zend\Diactoros\Stream;
use Zend\Expressive\Application;
use Zend\Expressive\MiddlewareFactory;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    // Test for ping action

    $app->get('/', new class() implements RequestHandlerInterface {
        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            return (new JsonResponse(['success' => true]))->withStatus(200);
        }
    });

    $app->get('/data/northwind-json', new class() implements RequestHandlerInterface {
        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            $file = realpath(dirname(dirname(getcwd())) . '/data') . '/northwind.json';

            return (new JsonResponse([]))->withBody(new Stream($file))->withStatus(200);
        }
    });

    $app->get('/data/northwind-xml', new class() implements RequestHandlerInterface {
        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            $file = realpath(dirname(dirname(getcwd())) . '/data') . '/northwind.xml';

            return (new XMLResponse(''))->withBody(new Stream($file))->withStatus(200);
        }
    });
};
