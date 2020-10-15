<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Applications;

use Bleeding\Http\ServerRequestFactoryInterface;
use DI\Container;
use LogicException;
use Narrowspark\HttpEmitter\SapiEmitter;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Relay\RelayBuilder;

use function Bleeding\makeResolver;

use const PHP_VERSION_ID;

/**
 * @package Bleeding\Applications
 */
abstract class WebApplication implements Application
{
    /**
     * {@inheritdoc}
     */
    public function createContainer(): Container
    {
        return ContainerFactory::create();
    }

    /**
     * Creates middleware queue processes Request and Response
     *
     * @param Container $container
     * @return (MiddlewareInterface|RequestHandlerInterface|string)[]
     */
    abstract public function createProcessQueue(Container $container): array;

    /**
     * {@inheritdoc}
     */
    final public function run(): void
    {
        if (PHP_VERSION_ID < 80000) {
            throw new LogicException('Bleeding Framework must run abobe PHP 8');
        }
        $this->setErrorHandler();
        $container = $this->createContainer();
        assert($container->has(ServerRequestFactoryInterface::class), 'exists own ServerRequestFactory');
        $serverRequestFactory = $container->get(ServerRequestFactoryInterface::class);

        $request = $serverRequestFactory->createFromGlobals();

        $queue = $this->createProcessQueue($container);
        assert(0 < count($queue), 'Assert queue has filled');

        $relayBuilder = new RelayBuilder(makeResolver($container));
        $response = $relayBuilder
            ->newInstance($queue)
            ->handle($request);
        (new SapiEmitter())->emit($response);

        restore_error_handler();
    }

    /**
     * Set global error handler
     */
    protected function setErrorHandler(): void
    {
        set_error_handler(function (
            int $errno,
            string $errstr,
            string $errfile,
            int $errline
        ) {
            // TODO: implementation
            return false;
        });
    }
}
