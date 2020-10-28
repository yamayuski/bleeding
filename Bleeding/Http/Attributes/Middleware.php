<?php

// phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Http\Attributes;

use Attribute;

/**
 * Path specific middleware
 * @package Bleeding\Http\Attributes
 */
#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD)]
class Middleware
{
    /** @var string[] */
    private array $middlewareNames = [];

    /**
     * Construct Middleware Information
     *
     * @param string|string[] $middlewares
     */
    public function __construct($middlewares)
    {
        $this->middlewareNames = (array)$middlewares;
    }

    /**
     * Get middleware names
     *
     * @return string[]
     */
    public function getMiddlewareNames(): array
    {
        return $this->middlewareNames;
    }
}
