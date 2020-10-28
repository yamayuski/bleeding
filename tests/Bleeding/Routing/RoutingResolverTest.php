<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Routing;
use Bleeding\Http\Exceptions\MethodNotAllowedException;
use Bleeding\Http\Exceptions\NotFoundException;
use Bleeding\Routing\RoutingResolver;
use DI\Container;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\StreamFactory;
use Tests\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @package Tests\Bleeding\Routing
 * @coversDefaultClass \Bleeding\Routing\RoutingResolver
 * @immutable
 */
final class RoutingResolverTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @uses \Bleeding\Exceptions\RuntimeException
     * @uses \Bleeding\Http\Attributes\Middleware
     * @uses \Bleeding\Http\Exceptions\InternalServerErrorException
     * @uses \Bleeding\Routing\Attributes\Get
     * @uses \Bleeding\Routing\Attributes\Post
     * @uses \Bleeding\Routing\CollectRoute
     * @uses \Bleeding\Routing\Route
     */
    public function testConstruct(): void
    {
        $container = new Container();
        $resolver = new RoutingResolver(__DIR__, $container);

        $this->assertInstanceOf(RoutingResolver::class, $resolver);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::handle
     * @uses \Bleeding\Exceptions\RuntimeException
     * @uses \Bleeding\Http\Attributes\Middleware
     * @uses \Bleeding\Http\Exceptions\InternalServerErrorException
     * @uses \Bleeding\Routing\Attributes\Get
     * @uses \Bleeding\Routing\Attributes\Post
     * @uses \Bleeding\Routing\CollectRoute
     * @uses \Bleeding\Routing\Route
     */
    public function testNotFound(): void
    {
        $container = new Container();
        $resolver = new RoutingResolver(__DIR__ . '/../../Stub/Routing', $container);

        $this->expectException(NotFoundException::class);
        $resolver->handle(new ServerRequest([], [], '/unknown-path', 'GET'));
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::handle
     * @uses \Bleeding\Exceptions\RuntimeException
     * @uses \Bleeding\Http\Attributes\Middleware
     * @uses \Bleeding\Http\Exceptions\InternalServerErrorException
     * @uses \Bleeding\Routing\Attributes\Get
     * @uses \Bleeding\Routing\Attributes\Post
     * @uses \Bleeding\Routing\CollectRoute
     * @uses \Bleeding\Routing\Route
     */
    public function testMethodNotAllowed(): void
    {
        $container = new Container();
        $resolver = new RoutingResolver(__DIR__ . '/../../Stub/Routing', $container);

        $this->expectException(MethodNotAllowedException::class);
        $resolver->handle(new ServerRequest([], [], '/', 'PUT'));
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::handle
     * @uses \Bleeding\Exceptions\RuntimeException
     * @uses \Bleeding\Http\Attributes\Middleware
     * @uses \Bleeding\Http\Exceptions\InternalServerErrorException
     * @uses \Bleeding\Routing\Attributes\Get
     * @uses \Bleeding\Routing\Attributes\Post
     * @uses \Bleeding\Routing\CollectRoute
     * @uses \Bleeding\Routing\InvokeController
     * @uses \Bleeding\Routing\MiddlewareResolver
     * @uses \Bleeding\Routing\Route
     */
    public function testHead(): void
    {
        $container = new Container();
        $container->set(ResponseFactoryInterface::class, new ResponseFactory);
        $container->set(StreamFactoryInterface::class, new StreamFactory);
        $resolver = new RoutingResolver(__DIR__ . '/../../Stub/Routing', $container);

        $response = $resolver->handle(new ServerRequest([], [], '/middleware', 'HEAD'));

        $this->assertSame('', (string)$response->getBody());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::handle
     * @uses \Bleeding\Exceptions\RuntimeException
     * @uses \Bleeding\Http\Attributes\Middleware
     * @uses \Bleeding\Http\Exceptions\InternalServerErrorException
     * @uses \Bleeding\Routing\Attributes\Get
     * @uses \Bleeding\Routing\Attributes\Post
     * @uses \Bleeding\Routing\CollectRoute
     * @uses \Bleeding\Routing\InvokeController
     * @uses \Bleeding\Routing\MiddlewareResolver
     * @uses \Bleeding\Routing\Route
     */
    public function testGet(): void
    {
        $container = new Container();
        $container->set(ResponseFactoryInterface::class, new ResponseFactory);
        $container->set(StreamFactoryInterface::class, new StreamFactory);
        $resolver = new RoutingResolver(__DIR__ . '/../../Stub/Routing', $container);

        $response = $resolver->handle(new ServerRequest([], [], '/', 'GET'));

        $this->assertSame('{"Hello":"world"}', (string)$response->getBody());
    }
}
