<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Routing;

use Bleeding\Http\Attributes\Get;
use Bleeding\Http\Attributes\Post;
use Bleeding\Http\Exceptions\MethodNotAllowedException;
use Bleeding\Http\Exceptions\NotFoundException;
use DI\Container;
use JsonSerializable;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionFunction;
use Relay\RelayBuilder;

use function array_key_exists;
use function Bleeding\makeResolver;
use function strtoupper;
use function trim;

class RoutingResolver implements RequestHandlerInterface
{
    /** @var string */
    protected string $baseDir;

    /** @var Container */
    protected Container $container;

    /**
     * @param string $baseDir base controller directory
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
            throw NotFoundException::createWithoutCode('Path not found', compact('path', 'method'));
        }

        if (!array_key_exists($method, $paths[$path])) {
            // TODO: Support OPTIONS and HEAD
            throw MethodNotAllowedException::createWithoutCode('HTTP method not allowed', compact('path', 'method'));
        }

        $route = $paths[$path][$method];

        $queue = [];
        foreach ($route->getMiddlewares() as $middleware) {
            $queue[] = $middleware;
        }

        $container = $this->container;

        // Main controller invoke
        $queue[] = function (ServerRequestInterface $request) use ($route, $container): ResponseInterface {
            /** @var array|JsonSerializable|ResponseInterface|string|null $result */
            $result = $container->call($route->getFunc(), compact('request'));

            if ($result instanceof ResponseInterface) {
                return $result;
            }

            $response = $container->get(ResponseFactoryInterface::class)->createResponse(200);
            if (is_null($result) || $result === '') {
                $response->getBody()->write('{}');
            } elseif (is_array($result) || $result instanceof JsonSerializable) {
                $response->getBody()->write(json_encode($result));
            }
            return $response->withHeader('Content-Type', 'application/json; charset=UTF-8');
        };

        $relayBuilder = new RelayBuilder(makeResolver($container));
        return $relayBuilder
            ->newInstance($queue)
            ->handle($request);
    }
}
