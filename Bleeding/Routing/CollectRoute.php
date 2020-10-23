<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Routing;

use Bleeding\Http\Attributes\Get;
use Bleeding\Http\Attributes\Middleware;
use Bleeding\Http\Attributes\Post;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionFunction;
use SplFileInfo;

use function array_reduce;
use function is_callable;
use function is_null;
use function str_ends_with;
use function trim;

/**
 * @package Bleeding\Routing
 */
final class CollectRoute
{
    /**
     * List up all routes
     *
     * @todo PHP file caching
     * @return array
     */
    public static function collect(string $baseDir): array
    {
        // TODO: Collect from cache
        // TODO: multiple baseDir
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($baseDir),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $paths = [];

        foreach ($iterator as $file) {
            $route = self::checkFile($file);

            if (!is_null($route)) {
                assert(!isset($paths[$route->getPath()][$route->getMethod()]), 'path is not conflicted');
                $paths[$route->getPath()][$route->getMethod()] = $route;
                if ($route->getMethod() === 'GET') {
                    // Add HEAD routing
                    $paths[$route->getPath()]['HEAD'] = $route;
                }
            }
        }

        return $paths;
    }

    /**
     * @internal
     * @param SplFileInfo $file
     * @return ?Route
     */
    private static function checkFile(SplFileInfo $file): ?Route
    {
        if (!str_ends_with($file->getBaseName(), '.php')) {
            return null;
        }

        $func = require $file->getRealPath();
        assert(is_callable($func), 'controller is callable');

        /** @psalm-suppress InvalidArgument */
        $ref = new ReflectionFunction($func);
        assert(0 < count($ref->getAttributes()), 'controller has attribute');

        /** @var string[] $middlewares */
        $middlewares = [];
        if (0 < count($ref->getAttributes(Middleware::class))) {
            $attr = $ref->getAttributes(Middleware::class)[0]->newInstance();
            $middlewares = $attr->getMiddlewareNames();
            assert(
                array_reduce($middlewares, fn (bool $carry, string $item) => ($carry && class_exists($item)), true),
                'All middleware class exists'
            );
        }

        $attr = null;
        if (0 < count($ref->getAttributes(Get::class))) {
            $attr = $ref->getAttributes(Get::class)[0]->newInstance();
        } elseif (0 < count($ref->getAttributes(Post::class))) {
            $attr = $ref->getAttributes(Post::class)[0]->newInstance();
        } else {
            return null;
        }

        $path = '/' . trim($attr->getPath(), '/');
        return new Route($path, $attr->getMethodName(), $func, $file->getRealPath(), $middlewares);
    }
}
