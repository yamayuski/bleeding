<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Routing;

use Bleeding\Exceptions\RuntimeException;
use Bleeding\Routing\CollectRoute;
use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Routing
 * @immutable
 * @coversDefaultClass \Bleeding\Routing\CollectRoute
 */
final class CollectRouteTest extends TestCase
{
    /**
     * @test
     * @covers ::collect
     * @covers ::checkFile
     * @covers ::getMiddlewares
     * @covers ::getAttribute
     * @uses \Bleeding\Http\Attributes\Middleware
     * @uses \Bleeding\Routing\Attributes\Get
     * @uses \Bleeding\Routing\Attributes\Post
     * @uses \Bleeding\Routing\Route
     */
    public function testCollect(): void
    {
        $baseDir = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'Stub', 'Routing']);
        $paths = CollectRoute::collect($baseDir);

        $this->assertSame(['/', '/middleware'], array_keys($paths));
        $this->assertSame(['POST', 'GET', 'HEAD'], array_keys($paths['/']));
    }
}
