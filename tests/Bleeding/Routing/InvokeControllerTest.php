<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Routing;

use DI\Container;
use Bleeding\Exceptions\RuntimeException;
use Bleeding\Routing\InvokeController;
use Bleeding\Routing\Route;
use JsonSerializable;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Stringable;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Routing
 * @immutable
 * @coversDefaultClass \Bleeding\Routing\InvokeController
 */
final class InvokeControllerTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::handle
     * @uses \Bleeding\Routing\Route
     */
    public function testRawResponse(): void
    {
        $response = new Response();
        $route = new Route('/', 'GET', fn ($request, $route) => $response, '', []);
        $container = new Container();
        $request = new ServerRequest();
        $invoke = new InvokeController($route, $container);

        $actual = $invoke->handle($request);

        $this->assertSame($response, $actual);
    }

    /**
     * Main Data provider
     */
    public function mainDataProvider(): array
    {
        return [
            'array' => [
                ['Hello' => 'world'],
                '{"Hello":"world"}',
            ],
            'JsonSerializable' => [
                new class implements JsonSerializable {
                    public function jsonSerialize(): array { return ['Hello' => 'world2']; }
                },
                '{"Hello":"world2"}',
            ],
            'null' => [
                null,
                '{}',
            ],
            'empty string' => [
                '',
                '{}',
            ],
            'string' => [
                '[]',
                '[]',
            ],
            'Stringable' => [
                new class implements Stringable {
                    public function __toString(): string { return '{"Hello":"world3"}'; }
                },
                '{"Hello":"world3"}',
            ]
        ];
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::handle
     * @covers ::writeResponse
     * @uses \Bleeding\Routing\Route
     * @dataProvider mainDataProvider
     */
    public function testWriteResponse(mixed $output, string $expected): void
    {
        $response = new Response();
        $route = new Route('/', 'GET', fn ($request, $route) => $output, '', []);
        $container = new Container();
        $container->set(ResponseFactoryInterface::class, new ResponseFactory);
        $request = new ServerRequest();
        $invoke = new InvokeController($route, $container);

        $actual = $invoke->handle($request);

        $this->assertSame($expected, (string)$actual->getBody());
        $this->assertSame('application/json; charset=utf-8', $actual->getHeaderLine('Content-Type'));
        $this->assertSame((int)strlen($expected), (int)$actual->getBody()->getSize());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::handle
     * @covers ::writeResponse
     * @uses \Bleeding\Routing\Route
     */
    public function testUnknownResponse(): void
    {
        $response = new Response();
        $route = new Route('/', 'GET', fn ($request, $route) => 0, '', []);
        $container = new Container();
        $container->set(ResponseFactoryInterface::class, new ResponseFactory);
        $request = new ServerRequest();
        $invoke = new InvokeController($route, $container);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Controller response must be ResponseInterface|JsonSerializable|Stringable|array|string|null, got int');
        $invoke->handle($request);
    }
}
