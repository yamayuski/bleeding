<?php

/**
 * Middleware Resolver
 */

declare(strict_types=1);

namespace Bleeding\Routing;

use Bleeding\Exceptions\RuntimeException;
use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

use function class_exists;
use function is_callable;
use function is_string;

/**
 * Create RequestHandlerInterface|MiddlewareInterface resolver using container
 * @immutable
 */
final class MiddlewareResolver
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(
        private ContainerInterface $container
    ) {}

    /**
     * Create instance resolver
     * @return callable
     */
    public function createResolver(): callable
    {
        return function (mixed $entry): mixed {
            if ($this->isValidInstance($entry)) {
                return $entry;
            }
            if (!is_string($entry) || !class_exists($entry)) {
                throw RuntimeException::create('Cannot resolve Middleware entry: ' . $entry, 1);
            }
            if (!$this->container->has($entry)) {
                throw new LogicException('Unknown Middleware in Container: ' . $entry);
            }
            $entryInstance = $this->container->get($entry);
            assert($this->isValidInstance($entryInstance), 'Assert $entryInstance is valid middleware');
            return $entryInstance;
        };
    }

    /**
     * validate entry is valid instance
     * @param mixed $entry
     * @return bool
     */
    private function isValidInstance(mixed $entry): bool
    {
        return $entry instanceof MiddlewareInterface ||
            $entry instanceof RequestHandlerInterface ||
            is_callable($entry);
    }
}
