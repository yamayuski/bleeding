<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Routing;

use Bleeding\Routing\Route;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Routing
 * @coversDefaultClass \Bleeding\Routing\Route
 * @immutable
 */
final class RouteTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::getPath
     * @covers ::getMethod
     * @covers ::getFunc
     * @covers ::getFilePath
     * @covers ::getMiddlewares
     */
    public function testConstruct(): void
    {
        $route = new Route(
            $path = '/',
            $method = 'GET',
            $func = fn() => true,
            $filePath = 'path',
            $middlewares = []
        );

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame($path, $route->getPath());
        $this->assertSame($method, $route->getMethod());
        $this->assertSame($func, $route->getFunc());
        $this->assertSame($filePath, $route->getFilePath());
        $this->assertSame($middlewares, $route->getMiddlewares());
    }
}
