<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding;

use Bleeding\Http\HttpServiceProvider;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

/**
 * ContainerFactory
 * @package Bleeding
 */
class ContainerFactory
{
    /**
     * Create Container
     *
     * @return ContainerInterface
     */
    public static function create(): ContainerInterface
    {
        $builder = new ContainerBuilder();
        $builder->useAttributes(true);

        static::addDefinitions($builder);

        // TODO: optimization options

        return $builder->build();
    }

    /**
     * Add container definitions
     *
     * @param ContainerBuilder $builder
     * @return void
     */
    public static function addDefinitions(ContainerBuilder $builder): void
    {
        $resolvePath = function (...$args): string {
            return implode(DIRECTORY_SEPARATOR, [__DIR__, ...$args, 'definitions.php']);
        };

        $builder->addDefinitions($resolvePath('Http'));
    }
}
