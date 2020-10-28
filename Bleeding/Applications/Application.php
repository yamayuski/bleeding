<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Applications;

/**
 * Main application interface
 * @package Bleeding\Applications
 */
interface Application
{
    /** @var string APP_NAME Application Name */
    public const APP_NAME = 'Bleeding';

    /** @var string APP_VERSION Application Version */
    public const APP_VERSION = '1.0.0';

    /**
     * Run application
     *
     * @return int exitCode
     */
    public function run(): int;
}
