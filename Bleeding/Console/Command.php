<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Console;

/**
 * Command
 *
 * @package Bleeding\Console
 */
final class Command
{
    /**
     * @param string $path
     * @param callable $func
     */
    public function __construct(
        private string $definition,
        private $func
    ) {}

    /**
     * get command definition
     * @return string
     */
    public function getDefinition(): string
    {
        return $this->definition;
    }

    /**
     * get controller function
     * @return callable
     */
    public function getFunc(): callable
    {
        return $this->func;
    }
}
