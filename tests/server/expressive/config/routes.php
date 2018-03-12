<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\TextResponse;
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

    // For smoke tests
    $app->get('/data/northwind.{format:json|xml}', new class() implements RequestHandlerInterface {
        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            // hardcoded for smoke tests is okay
            $dataPath = realpath(dirname(dirname(getcwd())) . '/data');
            switch ($request->getAttribute('format')) {
                case 'xml':
                    $response = (new Response())
                        ->withBody(new Stream("$dataPath/northwind.xml"))
                        ->withHeader('content-type', 'application/xml')
                        ->withStatus(200);
                    break;
                case 'json':
                    $response = (new Response())
                        ->withBody(new Stream("$dataPath/northwind.json"))
                        ->withHeader('content-type', 'application/json')
                        ->withStatus(200);
                    break;
                default:
                    $response = (new TextResponse('Error'))->withStatus(500);
            }

            return $response;
        }
    });
};
