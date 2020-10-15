<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Http\Exceptions;

use Bleeding\Exceptions\RuntimeException;
use Throwable;

/**
 * Raw HTTP Exception
 * @package Bleeding\Http\Exceptions
 */
class HttpException extends RuntimeException
{
    /** @var int CODE */
    protected const CODE = 500;

    /**
     * Create contextual Exception
     *
     * @param string $message
     * @param array $context
     * @param ?Throwable $previous
     * @return static
     */
    public static function createWithoutCode(
        string $message,
        array $context = [],
        ?Throwable $previous = null
    )/*FIXME : static*/ {
        $exception = new static($message, static::CODE, $previous);
        $exception->setContext($context);

        return $exception;
    }
}
