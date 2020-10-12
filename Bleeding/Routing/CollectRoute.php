<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Routing;

use Bleeding\Http\Attributes\AfterMiddleware;
use Bleeding\Http\Attributes\BeforeMiddleware;
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

final class CollectRoute
{
    /**
     * List up routes
     *
     * @todo Caching
     * @return array
     */
    public static function collect(string $baseDir): array
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($baseDir),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $paths = [];

        foreach ($iterator as $file) {
            if (!str_ends_with($file->getBaseName(), '.php')) {
                continue;
            }

            $func = require $file->getRealPath();
            assert(is_callable($func), 'Assert controller is callable');

            $ref = new ReflectionFunction($func);
            assert(0 < count($ref->getAttributes()), 'Assert controller has attribute');

            $middlewares = [];
            if (0 < count($ref->getAttributes(Middleware::class))) {
                $attr = $ref->getAttributes(Middleware::class)[0]->newInstance();
                $middlewares = $attr->getMiddlewareNames();
                assert(array_reduce($middlewares, fn ($carry, $item) => ($carry && class_exists($item)), true));
            }

            if (0 < count($ref->getAttributes(Get::class))) {
                $attr = $ref->getAttributes(Get::class)[0]->newInstance();
                $path = '/' . trim($attr->getPath(), '/');
                assert(!isset($paths[$path]['GET']), 'Assert path not conflicted');
                $paths[$path]['GET'] = new Route($path, 'GET', $func, $file->getRealPath(), $middlewares);
            } elseif (0 < count($ref->getAttributes(Post::class))) {
                $attr = $ref->getAttributes(Post::class)[0]->newInstance();
                $path = '/' . trim($attr->getPath(), '/');
                assert(!isset($paths[$path]['POST']), 'Assert path not conflicted');
                $paths[$path]['POST'] = new Route($path, 'POST', $func, $file->getRealPath(), $middlewares);
            }
        }

        return $paths;
    }
}
