<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Applications;

use LogicException;
use DI\Container;

use const PHP_VERSION_ID;

/**
 * @package Bleeding\Applications
 */
class ConsoleApplication implements Application
{
    /**
     * {@inheritdoc}
     */
    public function createContainer(): Container
    {
        return ContainerFactory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function run(): void
    {
        if (PHP_VERSION_ID < 80000) {
            throw new LogicException('Bleeding Framework must run abobe PHP 8');
        }
        $container = $this->createContainer();

        // TODO: implementation
    }
}
