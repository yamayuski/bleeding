<?php declare(strict_types=1);

/**
 * Middleware Resolver
 */

namespace Bleeding\Entrypoint;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * @param ContainerInterface $container
 * @return callable
 */
function _404Handler(ContainerInterface $container): callable {
    return function (ServerRequestInterface $request) use ($container): ResponseInterface {
        assert($container->has(ResponseFactoryInterface::class), 'ResponseFactoryInterface exists in container');
        return $container->get(ResponseFactoryInterface::class)->createResponse(404, 'Not found');
    };
};
