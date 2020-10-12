<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Http\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

/**
 * Process Runtime Error
 * @package Bleeding\Http\Middlewares
 */
final class ProcessErrorMiddleware implements MiddlewareInterface
{
    /**
     * constructor
     *
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $throwable) {
            // TODO: implementation
            $code = $throwable->getCode();
            $response = $this->responseFactory->createResponse(($code > 100 && $code < 600) ? $code : 500);
            $response->getBody()->write(json_encode([
                'message' => $throwable->getMessage(),
                'code' => $throwable->getCode(),
                'exception' => $throwable->getPrevious(),
            ]));
            return $response;
        }
    }
}
