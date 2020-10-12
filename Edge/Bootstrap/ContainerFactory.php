<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Edge\Bootstrap;

use Bleeding\ContainerFactory as ContainerFactoryBase;
use DI\ContainerBuilder;

/**
 * @package Edge\Bootstrap
 */
final class ContainerFactory extends ContainerFactoryBase
{
    /**
     * {@inheritdoc}
     */
    public static function addDefinitions(ContainerBuilder $builder): void
    {
        parent::addDefinitions($builder);

        $resolvePath = function (...$args): string {
            return implode(DIRECTORY_SEPARATOR, [__DIR__, '..', ...$args, 'definitions.php']);
        };
    }
}
