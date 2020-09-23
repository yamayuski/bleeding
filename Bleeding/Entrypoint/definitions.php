<?php declare(strict_types=1);

/**
 * Entrypoint DI Definitions
 */

namespace Bleeding\Entrypoint;

use function DI\get;

return [
    \Psr\Http\Message\ResponseFactoryInterface::class => get(\Laminas\Diactoros\ResponseFactory::class),
    \Psr\Http\Message\StreamFactoryInterface::class => get(\Laminas\Diactoros\StreamFactory::class),
];
