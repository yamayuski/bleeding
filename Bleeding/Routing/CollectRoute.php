<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Routing;

use Bleeding\Http\Attributes\Middleware;
use Bleeding\Routing\Attributes\Get;
use Bleeding\Routing\Attributes\Post;
use LogicException;
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
 * @immutable
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
        assert(is_callable($func), "controller {$file->getRealPath()} is callable");

        /** @psalm-suppress InvalidArgument */
        $ref = new ReflectionFunction($func);
        assert(0 < count($ref->getAttributes()), "Controller {$file->getRealPath()} has attribute");

        $middlewares = self::getMiddlewares($ref);
        $attr = self::getAttribute($ref);

        return new Route(
            $attr->getPath(),
            $attr->getMethodName(),
            $func,
            $file->getRealPath(),
            $middlewares
        );
    }

    /**
     * @param ReflectionFunction $ref
     * @return string[]
     */
    private static function getMiddlewares(ReflectionFunction $ref): array
    {
        $middlewares = [];
        if (0 === count($ref->getAttributes(Middleware::class))) {
            return $middlewares;
        }
        $attr = $ref->getAttributes(Middleware::class)[0]->newInstance();
        $middlewares = $attr->getMiddlewareNames();
        assert(
            array_reduce(
                $middlewares,
                fn (bool $carry, string $item) => ($carry && class_exists($item)),
                true
            ),
            'All middleware class exists'
        );

        return $middlewares;
    }

    /**
     * Get Get|Post Attribute
     * @param ReflectionFunction $ref
     * @return Get|Post
     */
    private static function getAttribute(ReflectionFunction $ref): Get|Post
    {
        $attr = $ref->getAttributes(Get::class);
        if (1 === count($attr)) {
            return $attr[0]->newInstance();
        }
        $attr = $ref->getAttributes(Post::class);
        if (1 === count($attr)) {
            return $attr[0]->newInstance();
        }
        throw new LogicException('Unknown routing funciton found: ' . $ref->getFileName()); // @codeCoverageIgnore
    }
}
