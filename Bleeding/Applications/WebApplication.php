<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Applications;

use Bleeding\Http\ServerRequestFactoryInterface;
use Bleeding\Routing\MiddlewareResolver;
use DI\Container;
use Narrowspark\HttpEmitter\SapiEmitter;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Relay\RelayBuilder;

use function assert;
use function count;
use function fastcgi_finish_request;
use function function_exists;
use function headers_sent;

/**
 * @package Bleeding\Applications
 */
abstract class WebApplication implements Application
{
    /**
     * {@inheritdoc}
     */
    public function createLogger(): LoggerInterface
    {
        return LoggerFactory::create('Bleeding');
    }

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
    abstract protected function createProcessQueue(Container $container): array;

    /**
     * Create ServerRequestInterface
     * @param ContainerInterface $container
     * @return ServerRequestInterface
     */
    protected function createServerRequest(ContainerInterface $container): ServerRequestInterface
    {
        /** @var ServerRequestFactoryInterface $factory */
        $factory = $container->get(ServerRequestFactoryInterface::class);
        return $factory->createFromGlobals();
    }

    /**
     * {@inheritdoc}
     */
    final public function run(): int
    {
        $logger = $this->createLogger();
        $errorHandler = (new ErrorHandler($logger));
        $errorHandler->setErrorHandler();

        $container = $this->createContainer();
        $container->set(LoggerInterface::class, $logger);
        $container->set(ContainerInterface::class, $container);

        $queue = $this->createProcessQueue($container);
        assert(0 < count($queue), 'queue has filled');

        $request = $this->createServerRequest($container);
        $response = (new RelayBuilder((new MiddlewareResolver($container))->createResolver()))
            ->newInstance($queue)
            ->handle($request);

        if (strpos(PHP_SAPI, 'cli') === false && strpos(PHP_SAPI, 'phpdbg') === false) {
            // @codeCoverageIgnoreStart
            (new SapiEmitter())->emit($response);
            assert(headers_sent());

            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            }
            // @codeCoverageIgnoreEnd
        }

        $errorHandler->restoreErrorHandler();
        return 0;
    }
}
