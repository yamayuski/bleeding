<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Applications;

use LogicException;
use DI\Container;
use Monolog\Logger;

use const PHP_VERSION_ID;

/**
 * @package Bleeding\Applications
 */
abstract class ConsoleApplication implements Application
{
    /**
     * {@inheritdoc}
     */
    public function createLogger(): Logger
    {
        return LoggerFactory::create('Bleeding');
    }

    /**
     * {@inheritdoc}
     */
    public function createContainer(): Container
    {
        return ContainerFactory::create();
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getBaseDirectory(): string;

    /**
     * {@inheritdoc}
     */
    public function run(): void
    {
        if (PHP_VERSION_ID < 80000) {
            throw new LogicException('Bleeding Framework must run abobe PHP 8');
        }
        $container = $this->createContainer();

        // TODO: implementation
    }
}
