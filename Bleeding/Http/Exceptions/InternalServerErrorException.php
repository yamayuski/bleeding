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
class InternalServerErrorException extends RuntimeException
{
    /** @var string MESSAGE */
    protected const MESSAGE = 'Internal Server Error';

    /** @var int CODE */
    protected const CODE = 500;

    /**
     * Create contextual Exception
     *
     * @param array $context
     * @param ?Throwable $previous
     * @return static
     */
    public static function createWithMessage(
        string $message,
        array $context = [],
        ?Throwable $previous = null
    ): static {
        $exception = new static($message, static::CODE, $previous);
        $exception->setContext($context);

        return $exception;
    }

    /**
     * Create contextual Exception
     *
     * @param array $context
     * @param ?Throwable $previous
     * @return static
     */
    public static function createWithContext(
        array $context = [],
        ?Throwable $previous = null
    ): static {
        $exception = new static(static::MESSAGE, static::CODE, $previous);
        $exception->setContext($context);

        return $exception;
    }
}
