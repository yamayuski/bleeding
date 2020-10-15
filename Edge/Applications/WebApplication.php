<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Edge\Applications;

use Bleeding\Applications\WebApplication as WebApplicationBase;

use DI\Container;

/**
 * @package Edge\Applications
 */
class WebApplication extends WebApplicationBase {
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
    public function createProcessQueue(Container $container): array
    {
        $baseDir = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'Controllers']);

        $queue = [];
        $queue[] = \Bleeding\Http\Middlewares\ProcessErrorMiddleware::class;
        $queue[] = \Bleeding\Http\Middlewares\ParseBodyMiddleware::class;
        $queue[] = \Bleeding\Http\Middlewares\AwareContentLengthMiddleware::class;
        $queue[] = new \Bleeding\Routing\RoutingResolver($baseDir, $container);

        return $queue;
    }
};
