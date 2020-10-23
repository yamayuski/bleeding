<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Edge\Applications;

use Bleeding\Applications\WebApplication as WebApplicationBase;
use Bleeding\Http\Middlewares\ParseBodyMiddleware;
use Bleeding\Http\Middlewares\ProcessErrorMiddleware;
use Bleeding\Routing\RoutingResolver;
use DI\Container;

use const DIRECTORY_SEPARATOR;

/**
 * @package Edge\Applications
 */
class WebApplication extends WebApplicationBase
{
    /**
     * {@inheritdoc}
     */
    public function createContainer(): Container
    {
        return ContainerFactory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseDirectory(): string
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..']);
    }

    public function getCacheDirectory(): string
    {
        return $this->getBaseDirectory() . DIRECTORY_SEPARATOR . 'cache';
    }

    /**
     * {@inheritdoc}
     */
    public function createProcessQueue(Container $container): array
    {
        $baseDir = implode(DIRECTORY_SEPARATOR, [$this->getBaseDirectory(), 'Edge', 'Controllers']);

        // register main Middlewares
        $queue = [
            ProcessErrorMiddleware::class,
            ParseBodyMiddleware::class,
            new RoutingResolver($baseDir, $container),
        ];

        return $queue;
    }
};
