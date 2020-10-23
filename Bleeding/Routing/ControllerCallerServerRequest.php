<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Routing;

use DI\Container;
use JsonSerializable;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_array;
use function is_null;
use function is_string;
use function json_encode;

/**
 * Main Userland Controller invoker
 * @package Bleeding\Routing
 */
final class ControllerCallerServerRequest implements RequestHandlerInterface
{
    /**
     * @param Route $route
     * @param Container $container
     */
    public function __construct(
        private Route $route,
        private Container $container
    ) {}

        /**
         * {@inheritdoc}
         */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $this->route;

        /** @var array|JsonSerializable|ResponseInterface|string|null $result */
        $result = $this->container->call($this->route->getFunc(), compact('route', 'request'));

        if ($result instanceof ResponseInterface) {
            // Response has been created in controller
            return $result;
        }

        /** @var ResponseInterface $response */
        $response = $this->container->get(ResponseFactoryInterface::class)->createResponse(200);

        match (true) {
            is_array($result), $result instanceof JsonSerializable =>
                $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)),
            is_null($result), $result === '' =>
                $response->getBody()->write('{}'),
            is_string($result) =>
                $response->getBody()->write($result),
            default => throw new LogicException(
                'Controller response must be array|JsonSerializable|ResponseInterface|string|null, got ' . get_debug_type($result),
                500
            ),
        };

        return $response
            ->withHeader('Content-Type', 'application/json; charset=UTF-8')
            ->withHeader('Content-Length', (string)$response->getBody()->getSize());
    }
}
