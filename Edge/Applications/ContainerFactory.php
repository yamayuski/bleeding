<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Edge\Applications;

use Bleeding\Applications\ContainerFactory as ContainerFactoryBase;
use DI\ContainerBuilder;

/**
 * @package Edge\Applications
 */
final class ContainerFactory extends ContainerFactoryBase
{
    /**
     * {@inheritdoc}
     */
    protected static function addDefinitions(ContainerBuilder $builder): void
    {
        parent::addDefinitions($builder);

        $builder->addDefinitions(static::resolveDefinitionsPath('Edge', 'Repositories'));
    }

    /**
     * resolve definitions path
     *
     * @param string[] $args
     * @return string
     */
    private static function resolveDefinitionsPath(string ...$args): string
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', ...$args, 'definitions.php']);
    }
}
