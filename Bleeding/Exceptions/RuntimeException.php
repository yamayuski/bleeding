<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Exceptions;

use RuntimeException as RuntimeExceptionBase;
use Throwable;

/**
 * Raw Runtime Exception
 * @package Bleeding\Exceptions
 */
class RuntimeException extends RuntimeExceptionBase
{
    /** @var array $context */
    protected array $context = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(string $message, int $code, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Set exception context
     *
     * @param array $context
     * @return void
     */
    protected function setContext(array $context): void
    {
        $this->context = $context;
    }

    /**
     * Get exception context
     *
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Create contextual Exception
     *
     * @param string $message
     * @param int $code
     * @param array $context
     * @param ?Throwable $previous
     * @return static
     */
    public static function create(
        string $message,
        int $code,
        array $context = [],
        ?Throwable $previous = null
    )/*FIXME : static*/ {
        $exception = new static($message, $code, $previous);
        $exception->setContext($context);

        return $exception;
    }
}
