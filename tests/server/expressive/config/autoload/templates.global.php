<?php

declare(strict_types=1);
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Expressive\Twig\TwigEnvironmentFactory;
use Zend\Expressive\Twig\TwigRendererFactory;

return [
    'dependencies' => [
        'factories' => [
            Twig_Environment::class          => TwigEnvironmentFactory::class,
            TemplateRendererInterface::class => TwigRendererFactory::class,
        ],
    ],

    'templates' => [
        'extension' => 'html.twig',
        'paths'     => [
            'auth'   => ['templates/auth'],
            'pages'  => ['templates/pages'],
            'layout' => ['templates/layout'],
            'error'  => ['templates/error'],
        ],
    ],

    'twig' => [
        'cache_dir'      => 'data/cache/twig',
        'assets_url'     => '/',
        'assets_version' => 10,
        'extensions'     => [
            // extension service names or instances
        ],
        'globals' => [
            // Global variables passed to twig templates
            'ga_tracking' => 'UA-XXXXX-X'
        ],
    ],
];
