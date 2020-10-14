<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Support;

use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosInterface;

use const ENTRY_TIME;

/**
 * @package Bleeding\Support
 */
final class Clock
{
    /**
     * Respect ENTRY_TIME defined by entrypoint
     *
     * @return ChronosInterface
     */
    public static function entry(): ChronosInterface
    {
        $timestamp = ENTRY_TIME;

        return new Chronos('@' . intval($timestamp));
    }

    /**
     * Get now
     *
     * @return ChronosInterface
     */
    public static function now(): ChronosInterface
    {
        return Chronos::now();
    }
}
