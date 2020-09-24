<?php

/**
 * 200 ok Healthcheck handler
 */

declare(strict_types=1);

namespace Bleeding\Entrypoint;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

use function json_encode;

/**
 * @param StreamFactoryInterface $streamFactory
 * @param ResponseFactoryInterface $responseFactory
 * @return callable
 */
return function (StreamFactoryInterface $streamFactory, ResponseFactoryInterface $responseFactory): callable {
    /**
     * @param ServerRequestInterface $request
     * @return array
     */
    $okHandler = function (ServerRequestInterface $request): array {
        return ['Hello' => 'world'];
    };

    /**
     * @param ServerRequestInterface $request
     * @param mixed $next
     * @return ResponseInterface
     */
    return function (
        ServerRequestInterface $request,
        callable $next
    ) use (
        $streamFactory,
        $responseFactory,
        $okHandler
    ): ResponseInterface {
        if ($request->getUri()->getPath() !== '/') {
            return $next($request);
        }

        return $responseFactory
            ->createResponse(200, 'Ok')
            ->withHeader('Content-Type', 'application/json')
            ->withBody($streamFactory->createStream(json_encode($okHandler($request))));
    };
};
