<?php

// phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Console\Attributes;

use Attribute;

/**
 * Function that processes console command
 * @package Bleeding\Console\Attributes
 */
#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD)]
class Command
{
    /**
     * Command
     *
     * @param string $definition
     */
    public function __construct(private string $definition)
    {}

    /**
     * Get command definition
     * @return string
     */
    public function getDefinition(): string
    {
        return $this->definition;
    }
}
