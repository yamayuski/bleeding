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
     * List up routes
     *
     * @todo Caching
     * @return array
     */
    protected function listUp(): array
    {
        $dirIterator = new RecursiveDirectoryIterator($this->baseDir);
        $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);

        $paths = [];

        foreach ($iterator as $file) {
            if (!str_ends_with($file->getBaseName(), '.php')) {
                continue;
            }

            $func = require $file->getRealPath();
            assert(is_callable($func), 'Assert controller is callable');

            $ref = new ReflectionFunction($func);
            assert(0 < count($ref->getAttributes()), 'Assert controller has attribute');

            if (0 < count($ref->getAttributes(Get::class))) {
                $attr = $ref->getAttributes(Get::class)[0]->newInstance();
                $path = rtrim($attr->getPath(), '/');
                assert(!isset($paths[$path]['GET']), 'Assert path not conflicted');
                $paths[$path]['GET'] = $func;
            } elseif (0 < count($ref->getAttributes(Post::class))) {
                $attr = $ref->getAttributes(Post::class)[0]->newInstance();
                $path = rtrim($attr->getPath(), '/');
                assert(!isset($paths[$path]['POST']), 'Assert path not conflicted');
                $paths[$path]['POST'] = $func;
            }
        }

        return $paths;
    }

    /**
     * Resolve path
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $path = rtrim($request->getUri()->getPath(), '/');
        $method = strtoupper($request->getMethod());
        $paths = $this->listUp();

        if (!array_key_exists($path, $paths)) {
            throw NotFoundException::createWithoutCode('Path not found', compact('path', 'method'));
        }

        if (!array_key_exists($method, $paths[$path])) {
            // TODO: Support OPTIONS and HEAD
            throw MethodNotAllowedException::createWithoutCode('HTTP method not allowed', compact('path', 'method'));
        }

        $result = $this->container->call($paths[$path][$method], compact('request'));

        $response = $this->container->get(ResponseFactoryInterface::class)->createResponse(200, 'ok');
        if (is_array($result) || $result instanceof JsonSerializable) {
            $response->getBody()->write(json_encode($result));
        }
        return $response;
    }
}
