<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Applications;

use Psr\Log\LoggerInterface;

use function array_map;
use function compact;
use function debug_backtrace;
use function headers_sent;
use function json_encode;
use function restore_error_handler;
use function set_error_handler;
use function strpos;

use const DEBUG_BACKTRACE_IGNORE_ARGS;
use const JSON_UNESCAPED_UNICODE;
use const PHP_SAPI;

/**
 * catches PHP Errors and respond Error Response
 * @package Bleeding\Applications
 */
final class ErrorHandler
{
    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        private LoggerInterface $logger
    ) {}

    /**
     * Set global error handler
     * @return self
     */
    public function setErrorHandler(): self
    {
        set_error_handler([$this, 'handle']);

        return $this;
    }

    /**
     * Restore global error handler
     * @return void
     */
    public function restoreErrorHandler(): void
    {
        restore_error_handler();
    }

    /**
     * Set global error handler
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     */
    public function handle(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20);
        $trace = array_map(fn (array $arg) =>
            sprintf("%s%s%s() in %s:%s",
                $arg['class'] ?? '',
                $arg['type'] ?? '',
                $arg['function'] ?? '',
                $arg['file'] ?? '',
                $arg['line'] ?? ''
            ),
            $trace
        );

        $body = ['message' => $errstr, 'error' => compact('errno', 'errstr', 'errfile', 'errline', 'trace')];
        $bodyRaw = (strpos(PHP_SAPI, 'cli') !== false || getenv('DEBUG_MODE') === 'true') ? $body : ['message' => 'Internal Server Error'];
        $bodyString = json_encode($bodyRaw, JSON_UNESCAPED_UNICODE);
        $bodyLen = strlen($bodyString);

        if (error_reporting() === 0) {
            // through that error is suppressed by @
            $this->logger->debug($errstr, $body);
            return false;
        }

        $this->logger->error($errstr, $body);

        // respond HTTP
        if (strpos(PHP_SAPI, 'cli') === false && !headers_sent()) {
            // @codeCoverageIgnoreStart
            header('HTTP/1.1 500 Internal Server Error');
            header('content-type: application/json; charset=utf-8');
            header("content-length: ${bodyLen}");
            echo $bodyString;
            // @codeCoverageIgnoreEnd
        }

        return false;
    }
}
