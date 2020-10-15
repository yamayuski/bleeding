<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Applications;

use DI\Container;

/**
 * Main running application interface
 * @package Bleeding\Applications
 */
interface Application
{
    /**
     * Create container
     *
     * @return Container
     */
    public function createContainer(): Container;

    /**
     * Run application
     *
     * @return void
     */
    public function run(): void;
}
