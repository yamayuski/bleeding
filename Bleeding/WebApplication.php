<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding;

use Bleeding\Http\ServerRequestFactoryInterface;
use Bleeding\Routing\RoutingResolver;
use Narrowspark\HttpEmitter\SapiEmitter;
use Psr\Container\ContainerInterface;
use Relay\RelayBuilder;

use function Bleeding\Entrypoint\makeResolver;

/**
 * @package Bleeding
 */
class WebApplication implements Application
{
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
    public function run(): void
    {
        $container = $this->createContainer();
        $serverRequestFactory = $container->get(ServerRequestFactoryInterface::class);

        $request = $serverRequestFactory->createFromGlobals();
        $baseDir = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'Edge', 'Controllers']);
        $resolver = new RoutingResolver($baseDir, $container);

        $queue = [];
        // TODO: Add Global Middlewares
        $queue[] = $resolver;

        $relayBuilder = new RelayBuilder(makeResolver($container));
        $response = $relayBuilder
            ->newInstance($queue)
            ->handle($request);
        (new SapiEmitter())->emit($response);
    }
}
