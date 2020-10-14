<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Http\Middlewares;

use Bleeding\Http\Middlewares\AwareContentLengthMiddleware;
use Bleeding\Http\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Http\Middlewares
 * @coversDefaultClass AwareContentLengthMiddleware
 */
final class AwareContentLengthMiddlewareTest extends TestCase
{
    /**
     * @test
     * @covers ::process
     */
    public function testHasBody()
    {
        $container = $this->createContainer();
        $request = $container->get(ServerRequestFactoryInterface::class)->createFromGlobals();
        $responseBase = $container->get(ResponseFactoryInterface::class)->createResponse(200);
        $dummyHandler = new class ($responseBase) implements RequestHandlerInterface {
            private ResponseInterface $response;
            public function __construct(ResponseInterface $res) {
                $this->response = $res;
            }
            public function handle(ServerRequestInterface $request): ResponseInterface {
                return $this->response;
            }
        };

        $responseBase->getBody()->write('{}');
        $middleware = $container->get(AwareContentLengthMiddleware::class);

        $response = $middleware->process($request, $dummyHandler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(2, $response->getHeaderLine('Content-Length'));
    }

    /**
     * @test
     * @covers ::process
     */
    public function testNotHasBody()
    {
        $container = $this->createContainer();
        $request = $container->get(ServerRequestFactoryInterface::class)->createFromGlobals();
        $responseBase = $container->get(ResponseFactoryInterface::class)->createResponse(200);
        $dummyHandler = new class ($responseBase) implements RequestHandlerInterface {
            private ResponseInterface $response;
            public function __construct(ResponseInterface $res) {
                $this->response = $res;
            }
            public function handle(ServerRequestInterface $request): ResponseInterface {
                return $this->response;
            }
        };

        // $responseBase->getBody()->write('{}');
        $middleware = $container->get(AwareContentLengthMiddleware::class);

        $response = $middleware->process($request, $dummyHandler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(0, $response->getHeaderLine('Content-Length'));
    }
}
