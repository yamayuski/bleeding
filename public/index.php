<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

define('ENTRY_TIME', microtime(true));

// autoload
require implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'vendor', 'autoload.php']);

use Bleeding\Routing\RoutingResolver;
use Bleeding\WebApplication;
use Edge\Bootstrap\ContainerFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

// run application
$app = new class extends WebApplication {
    /**
     * {@inheritdoc}
     */
    public function createContainer(): ContainerInterface
    {
        return ContainerFactory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function createProcessQueue(ContainerInterface $container): array
    {
        $baseDir = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'Edge', 'Controllers']);

        $queue = [];
        $queue[] = \Bleeding\Http\Middlewares\ProcessErrorMiddleware::class;
        $queue[] = \Bleeding\Http\Middlewares\ParseBodyMiddleware::class;
        $queue[] = \Bleeding\Http\Middlewares\AwareContentLengthMiddleware::class;
        $queue[] = new RoutingResolver($baseDir, $container);

        return $queue;
    }
};

$app->run();
