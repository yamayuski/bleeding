<?php

/**
 * Container Builder
 */

declare(strict_types=1);

namespace Bleeding\Entrypoint;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

/**
 * @return ContainerInterface
 */
function makeContainer(): ContainerInterface
{
    $builder = new ContainerBuilder();

    $builder->addDefinitions(__DIR__ . DIRECTORY_SEPARATOR . 'definitions.php');

    return $builder->build();
}
