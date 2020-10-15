<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Applications;

use Bleeding\Http\HttpServiceProvider;
use DI\Container;
use DI\ContainerBuilder;

/**
 * ContainerFactory
 * @package Bleeding\Applications
 */
class ContainerFactory
{
    /**
     * Create Container
     *
     * @return Container
     */
    public static function create(): Container
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
    protected static function addDefinitions(ContainerBuilder $builder): void
    {
        $builder->addDefinitions(self::resolveDefinitionsPath('Bleeding', 'Http'));
    }

    /**
     * resolve definitions path
     *
     * @param string[] $args
     * @return string
     */
    protected static function resolveDefinitionsPath(string ...$args): string
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', ...$args, 'definitions.php']);
    }
}
