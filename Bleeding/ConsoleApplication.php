<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding;

use Psr\Container\ContainerInterface;

/**
 * @package Bleeding
 */
class ConsoleApplication implements Application
{
    /**
     * {@inheritdoc}
     */
    public function createContainer(): ContainerInterface
    {
        return ContainerFactory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function run(): void
    {
        $container = $this->createContainer();

        // TODO: implementation
    }
}
