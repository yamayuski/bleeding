<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Http\Middlewares;

use Bleeding\Http\Exceptions\MethodNotAllowedException;
use Bleeding\Http\Middlewares\ProcessErrorMiddleware;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Stream;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Http\Middlewares
 * @coversDefaultClass \Bleeding\Http\Middlewares\ProcessErrorMiddleware
 * @immutable
 */
final class ProcessErrorMiddlewareTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::process
     * @covers ::processThrowable
     */
    public function testProcessThrowable(): void
    {
        $request = new ServerRequest(
            [],
            [],
            '/',
            'GET',
            new Stream('php://temp', 'rw'),
            ['Content-Type' => 'application/json']
        );
        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                throw new \Exception('Error');
            }
        };
        $logger = new Logger('Bleeding Test');
        $loggerHandler = new TestHandler();
        $logger->pushHandler($loggerHandler);
        $middleware = new ProcessErrorMiddleware(new ResponseFactory(), $logger);

        $response = $middleware->process($request, $handler);

        $this->assertInstanceOf(MiddlewareInterface::class, $middleware);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertTrue($loggerHandler->hasErrorThatContains('Error'));
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::process
     * @covers ::processThrowable
     */
    public function testProcessThrowableOnNoDebugMode(): void
    {
        // no debug mode
        putenv('DEBUG_MODE=false');

        $request = new ServerRequest(
            [],
            [],
            '/',
            'GET',
            new Stream('php://temp', 'rw'),
            ['Content-Type' => 'application/json']
        );
        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                throw new \Exception('Error', 503);
            }
        };
        $logger = new Logger('Bleeding Test');
        $loggerHandler = new TestHandler();
        $logger->pushHandler($loggerHandler);
        $middleware = new ProcessErrorMiddleware(new ResponseFactory(), $logger);

        $response = $middleware->process($request, $handler);

        $this->assertInstanceOf(MiddlewareInterface::class, $middleware);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(503, $response->getStatusCode());
        $this->assertSame('{"message":"Internal Server Error"}', (string)$response->getBody());
        $this->assertTrue($loggerHandler->hasErrorThatContains('Error'));
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::process
     * @covers ::processThrowable
     * @uses \Bleeding\Exceptions\RuntimeException
     * @uses \Bleeding\Http\Exceptions\InternalServerErrorException
     */
    public function testProcessThrowableOnMethodNotAllowedException(): void
    {
        $request = new ServerRequest(
            [],
            [],
            '/',
            'GET',
            new Stream('php://temp', 'rw'),
            ['Content-Type' => 'application/json']
        );
        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                throw MethodNotAllowedException::createWithContext(['allow' => ['GET', 'POST']]);
            }
        };
        $logger = new Logger('Bleeding Test');
        $loggerHandler = new TestHandler();
        $logger->pushHandler($loggerHandler);
        $middleware = new ProcessErrorMiddleware(new ResponseFactory(), $logger);

        $response = $middleware->process($request, $handler);

        $this->assertInstanceOf(MiddlewareInterface::class, $middleware);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(405, $response->getStatusCode());
        $this->assertSame('GET,POST', $response->getHeaderLine('Allow'));
    }
}
