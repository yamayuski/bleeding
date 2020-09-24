<?php

/**
 * 404 Not found Handler
 */

declare(strict_types=1);

namespace Bleeding\Entrypoint;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * @param ResponseFactoryInterface $responseFactory
 * @return callable
 */
return function (ResponseFactoryInterface $responseFactory): callable {
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    return function (ServerRequestInterface $request) use ($responseFactory): ResponseInterface {
        return $responseFactory->createResponse(404, 'Not found');
    };
};
