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
use Monolog\Logger;
use Narrowspark\HttpEmitter\SapiEmitter;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Relay\RelayBuilder;

use function array_map;
use function assert;
use function Bleeding\makeResolver;
use function compact;
use function count;
use function debug_backtrace;
use function json_encode;
use function restore_error_handler;
use function set_error_handler;

use const PHP_VERSION_ID;

/**
 * @package Bleeding\Applications
 */
abstract class WebApplication implements Application
{
    /**
     * {@inheritdoc}
     */
    public function createLogger(): Logger
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
     * {@inheritdoc}
     */
    abstract public function getBaseDirectory(): string;

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
        $logger = $this->createLogger();
        $this->setErrorHandler($logger);
        $container = $this->createContainer();
        $container->set(LoggerInterface::class, $logger);
        $serverRequestFactory = $container->get(ServerRequestFactoryInterface::class);

        $request = $serverRequestFactory->createFromGlobals();

        $queue = $this->createProcessQueue($container);
        assert(0 < count($queue), 'queue has filled');

        $relayBuilder = new RelayBuilder(makeResolver($container));
        $response = $relayBuilder
            ->newInstance($queue)
            ->handle($request);
        (new SapiEmitter())->emit($response);

        $this->restoreErrorHandler();
    }

    /**
     * Set global error handler
     * @param Logger $logger
     */
    final protected function setErrorHandler(Logger $logger): void
    {
        set_error_handler(function (
            int $errno,
            string $errstr,
            string $errfile,
            int $errline
        ) use ($logger) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20);
            $backtrace = array_map(fn (array $arg) => "${arg['class']}${arg['type']}${arg['function']} in ${arg['file']}:${arg['line']}", $backtrace);

            $body = ['message' => $errstr, 'error' => compact('errno', 'errstr', 'errfile', 'errline', 'backtrace')];
            $debugMode = getenv('DEBUG_MODE') === 'true';
            $bodyString = json_encode($debugMode ? $body : ['message' => 'Internal Server Error'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            $bodyLen = strlen($bodyString);
            $logger->error($errstr, $body);

            // respond HTTP
            header('HTTP/1.1 500 Internal Server Error');
            header('content-type: application/json; charset=utf-8');
            header("content-length: ${bodyLen}");
            echo $bodyString;

            return false;
        });
    }

    /**
     * Restore global error handler
     */
    final protected function restoreErrorHandler(): void
    {
        restore_error_handler();
    }
}
