<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Applications;

use DI\Container;
use Monolog\Logger;

/**
 * Main application interface
 * @package Bleeding\Applications
 */
interface Application
{
    /**
     * Create logger
     *
     * @return Logger
     */
    public function createLogger(): Logger;

    /**
     * Create container
     *
     * @return Container
     */
    public function createContainer(): Container;

    /**
     * get application base directory
     *
     * @return string
     */
    public function getBaseDirectory(): string;

    /**
     * Run application
     *
     * @return void
     */
    public function run(): void;
}
