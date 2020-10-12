<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding;

use Psr\Container\ContainerInterface;

/**
 * Main running application interface
 * @package Bleeding
 */
interface Application
{
    /**
     * Create container
     *
     * @return ContainerInterface
     */
    public function createContainer(): ContainerInterface;

    /**
     * Run application
     *
     * @return void
     */
    public function run(): void;
}
