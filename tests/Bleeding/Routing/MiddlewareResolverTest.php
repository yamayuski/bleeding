<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Routing;

use Bleeding\Exceptions\RuntimeException;
use Bleeding\Routing\MiddlewareResolver;
use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Routing
 * @immutable
 * @coversDefaultClass \Bleeding\Routing\MiddlewareResolver
 */
final class MiddlewareResolverTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::createResolver
     * @covers ::isValidInstance
     */
    public function testClassCallable(): void
    {
        $container = new class implements ContainerInterface {
            public function get($id) {}
            public function has($id) {}
        };

        $resolver = (new MiddlewareResolver($container))->createResolver();
        $entry = fn () => true;

        $actual = $resolver($entry);

        $this->assertSame($entry, $actual);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::createResolver
     * @covers ::isValidInstance
     * @uses \Bleeding\Exceptions\RuntimeException
     */
    public function testClassNotFound(): void
    {
        $container = new class implements ContainerInterface {
            public function get($id) {}
            public function has($id) {}
        };

        $resolver = new MiddlewareResolver($container);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage('Cannot resolve Middleware entry: UnknownKlass');

        $resolver = $resolver->createResolver();
        $resolver('UnknownKlass');
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::createResolver
     * @covers ::isValidInstance
     */
    public function testClassHasNoEntry(): void
    {
        $container = new class implements ContainerInterface {
            public function get($id) {}
            public function has($id) { return false; }
        };

        $resolver = (new MiddlewareResolver($container))->createResolver();

        $this->expectException(LogicException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Unknown Middleware in Container: Tests\\Bleeding\\Routing\\MiddlewareResolverTest');

        $resolver(self::class);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::createResolver
     * @covers ::isValidInstance
     */
    public function testClassGetFromContainer(): void
    {
        $container = new class implements ContainerInterface {
            public function get($id) { return fn() => true; }
            public function has($id) { return true; }
        };

        $resolver = (new MiddlewareResolver($container))->createResolver();
        $entry = $resolver(self::class);

        $this->assertTrue(is_callable($entry));
    }
}
