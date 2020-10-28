<?php

// phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Routing\Attributes;

use Attribute;

use function trim;

/**
 * Function that processes HTTP GET Request
 * @package Bleeding\Routing\Attributes
 * @immutable
 */
#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD)]
class Get
{
    private string $path;

    /**
     * Construct GET Controller
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = '/' . trim($path, '/');
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * get Method name string
     *
     * @return string
     */
    public function getMethodName(): string
    {
        return 'GET';
    }
}
