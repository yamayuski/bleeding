<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Routing;

use Bleeding\Http\Exceptions\MethodNotAllowedException;
use Bleeding\Http\Exceptions\NotFoundException;
use DI\Container;
use JsonSerializable;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionFunction;
use Relay\RelayBuilder;

use function array_key_exists;
use function Bleeding\makeResolver;
use function strtoupper;
use function trim;

/**
 * Resolve routing and invoke main controller
 * @package Bleeding\Routing
 */
class RoutingResolver implements RequestHandlerInterface
{
    /** @var string */
    protected string $baseDir;

    /** @var Container */
    protected Container $container;

    /**
     * @param string $baseDir base controller directory
     * @param Container $container IoC Container
     */
    public function __construct(string $baseDir, Container $container)
    {
        $this->baseDir = $baseDir;
        $this->container = $container;
    }

    /**
     * Resolve path
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $path = '/' . trim($request->getUri()->getPath(), '/');
        $method = strtoupper($request->getMethod());
        $paths = CollectRoute::collect($this->baseDir);

        if (!array_key_exists($path, $paths)) {
            throw NotFoundException::createWithContext(compact('path', 'method'));
        }

        if (!array_key_exists($method, $paths[$path])) {
            // TODO: Support OPTIONS
            $allow = array_keys($paths[$path]);
            throw MethodNotAllowedException::createWithContext(compact('allow', 'path', 'method'));
        }

        $route = $paths[$path][$method];

        $queue = [];
        foreach ($route->getMiddlewares() as $middleware) {
            assert(class_exists($middleware));
            $queue[] = $middleware;
        }

        $container = $this->container;

        // Main controller invoke
        $queue[] = new ControllerCallerServerRequest($route, $container);

        $relayBuilder = new RelayBuilder(makeResolver($container));
        $response = $relayBuilder
            ->newInstance($queue)
            ->handle($request);

        if ($method === 'HEAD') {
            // fresh body
            return $response->withBody($container->get(StreamFactoryInterface::class)->createStream(''));
        }
        return $response;
    }
}
