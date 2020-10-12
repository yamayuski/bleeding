<?php

/**
 * Middleware Resolver
 */

declare(strict_types=1);

namespace Bleeding\Entrypoint;

use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use RuntimeException;

use function class_exists;
use function is_callable;
use function is_string;

/**
 * @param ContainerInterface $container IoC Container
 * @return callable
 */
function makeResolver(ContainerInterface $container): callable
{
    /**
     * Check the entry is valid Middleware(RequestHandler).
     *
     * @param mixed $entry
     * @return bool is valid
     */
    $isValidInstance = function ($entry): bool {
        return $entry instanceof MiddlewareInterface ||
            $entry instanceof RequestHandlerInterface ||
            is_callable($entry);
    };

    /**
     * @param mixed $entry middleware entry
     * @return mixed entry instance
     */
    return function ($entry) use ($container, $isValidInstance) {
        if ($isValidInstance($entry)) {
            return $entry;
        }
        if (is_string($entry) && class_exists($entry)) {
            if (!$container->has($entry)) {
                throw new LogicException('Unknown Middleware in Container: ' . $entry);
            }
            $entryInstance = $container->get($entry);
            assert($isValidInstance($entryInstance), 'Assert $entryInstance is valid middleware');
            return $entryInstance;
        }
        throw new RuntimeException('Cannot resolve Middleware entry: ' . $entry);
    };
}
