<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Applications;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * @package Bleeding\Applications
 */
class LoggerFactory
{
    /**
     * Create Logger
     * @return Logger
     */
    public static function create(string $name = 'Bleeding'): Logger
    {
        $logger = new Logger($name);
        $handler = new StreamHandler('php://stdout');
        $handler->setFormatter(new JsonFormatter());

        $logger->pushHandler($handler);

        return $logger;
    }
}
