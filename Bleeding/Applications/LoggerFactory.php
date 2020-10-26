<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Applications;

use Monolog\Formatter\JsonFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package Bleeding\Applications
 */
class LoggerFactory
{
    /**
     * Create Logger for Web
     * @return Logger
     */
    public static function create(string $name = 'Bleeding'): Logger
    {
        $handler = new StreamHandler('php://stdout');
        $handler->setFormatter(new JsonFormatter());

        $logger = new Logger($name);
        $logger->pushHandler($handler);

        return $logger;
    }
}
