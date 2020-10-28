<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Http\Middlewares;

use Bleeding\Http\Exceptions\BadRequestException;
use Bleeding\Http\Middlewares\ParseBodyMiddleware;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Stream;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Riverline\MultiPartParser\StreamedPart;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Http\Middlewares
 * @coversDefaultClass \Bleeding\Http\Middlewares\ParseBodyMiddleware
 * @immutable
 */
final class ParseBodyMiddlewareTest extends TestCase
{
    public function createRequestHandler($expected): RequestHandlerInterface
    {
        return new class ($expected) implements RequestHandlerInterface {
            public $expected;
            public function __construct($expected)
            {
                $this->expected = $expected;
            }
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                if ($request->getParsedBody() instanceof StreamedPart) {
                    assert($request->getParsedBody()->isMultipart());
                } else {
                    assert($request->getParsedBody() === $this->expected);
                }
                return new Response();
            }
        };
    }

    /**
     * @test
     * @covers ::process
     * @covers ::parseJson
     */
    public function testParseJson(): void
    {
        $request = new ServerRequest(
            [],
            [],
            '/',
            'GET',
            new Stream('php://temp', 'rw'),
            ['Content-Type' => 'application/json']
        );
        $request->getBody()->write('{"Hello":"world"}');
        $handler = $this->createRequestHandler(['Hello' => 'world']);
        $middleware = new ParseBodyMiddleware();

        $response = $middleware->process($request, $handler);

        $this->assertInstanceOf(MiddlewareInterface::class, $middleware);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @test
     * @covers ::process
     * @covers ::parseJson
     * @uses \Bleeding\Exceptions\RuntimeException
     * @uses \Bleeding\Http\Exceptions\InternalServerErrorException
     */
    public function testParseJsonFailed(): void
    {
        $request = new ServerRequest(
            [],
            [],
            '/',
            'GET',
            new Stream('php://temp', 'rw'),
            ['Content-Type' => 'application/json']
        );
        $request->getBody()->write('this is not json');
        $handler = $this->createRequestHandler(null);
        $middleware = new ParseBodyMiddleware();

        $this->expectException(BadRequestException::class);

        $middleware->process($request, $handler);
    }

    /**
     * @see https://github.com/Riverline/multipart-parser/blob/master/tests/Converters/PSR7Test.php
     */
    public function createBodyStream()
    {
        $content = <<<EOL
User-Agent: curl/7.21.2 (x86_64-apple-darwin)
Host: localhost:8080
Accept: */*
Content-Length: 1143
Expect: 100-continue
X-Multi-Line: line one
    line two with space
    line three with tab
Content-Type: multipart/form-data; boundary=----------------------------83ff53821b7c

------------------------------83ff53821b7c
Content-Disposition: form-data; name="foo"

bar
------------------------------83ff53821b7c--
EOL;

        list(, $body) = preg_split("/(\n){2}/", $content, 2);

        $stream = fopen('php://temp', 'rw');
        fwrite($stream, $body);

        rewind($stream);

        return $stream;
    }

    /**
     * @test
     * @covers ::process
     * @covers ::parseMultipart
     */
    public function testParseMultipart(): void
    {
        $request = new ServerRequest(
            [],
            [],
            '/',
            'GET',
            $this->createBodyStream(),
            ['Content-type' => 'multipart/form-data; boundary=----------------------------83ff53821b7c']
        );
        $handler = $this->createRequestHandler(['foo' => 'bar']);
        $middleware = new ParseBodyMiddleware();

        $response = $middleware->process($request, $handler);

        $this->assertInstanceOf(MiddlewareInterface::class, $middleware);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @test
     * @covers ::process
     * @covers ::parseForm
     */
    public function testParseForm(): void
    {

        $request = new ServerRequest(
            [],
            [],
            '/',
            'GET',
            new Stream('php://temp', 'rw'),
            ['Content-type' => 'application/x-www-form-urlencoded']
        );
        $request->getBody()->write('first=value&bar=baz');
        $handler = $this->createRequestHandler(['first' => 'value', 'bar' => 'baz']);
        $middleware = new ParseBodyMiddleware();

        $response = $middleware->process($request, $handler);

        $this->assertInstanceOf(MiddlewareInterface::class, $middleware);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @test
     * @covers ::process
     */
    public function testParseDefault(): void
    {
        $request = new ServerRequest(
            [],
            [],
            '/',
            'GET',
            new Stream('php://temp', 'rw'),
            ['Content-Type' => 'text/plain']
        );
        $handler = $this->createRequestHandler(null);
        $middleware = new ParseBodyMiddleware();

        $response = $middleware->process($request, $handler);

        $this->assertInstanceOf(MiddlewareInterface::class, $middleware);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
