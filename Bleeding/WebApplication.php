<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding;

use Bleeding\Http\ServerRequestFactoryInterface;
use Narrowspark\HttpEmitter\SapiEmitter;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Relay\RelayBuilder;

use function Bleeding\Entrypoint\makeResolver;

/**
 * @package Bleeding
 */
abstract class WebApplication implements Application
{
    /**
     * {@inheritdoc}
     */
    public function createContainer(): ContainerInterface
    {
        return ContainerFactory::create();
    }

    /**
     * Creates queue processes Request and Response
     * @param ServerRequestInterface $request
     * @param ContainerInterface $container
     * @return (MiddlewareInterface|RequestHandlerInterface)[]
     */
    public abstract function createProcessQueue(ServerRequestInterface $request, ContainerInterface $container): array;

    /**
     * {@inheritdoc}
     */
    public function run(): void
    {
        $this->setErrorHandler();
        $container = $this->createContainer();
        $serverRequestFactory = $container->get(ServerRequestFactoryInterface::class);

        $request = $serverRequestFactory->createFromGlobals();

        $queue = $this->createProcessQueue($request, $container);

        $relayBuilder = new RelayBuilder(makeResolver($container));
        $response = $relayBuilder
            ->newInstance($queue)
            ->handle($request);
        (new SapiEmitter())->emit($response);
    }

    public function setErrorHandler(): void
    {
        set_error_handler(function (
            int $errno,
            string $errstr,
            string $errfile,
            int $errline) {
            return true;
        });
    }
}
