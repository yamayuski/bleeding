<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Applications;

use Bleeding\Console\CollectCommands;
use DI\Container;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Silly\Application as ApplicationBase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * @package Bleeding\Applications
 */
abstract class ConsoleApplication implements Application
{
    /** @var InputInterface $input */
    protected InputInterface $input;

    /** @var OutputInterface $output */
    protected OutputInterface $output;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(
        ?InputInterface $input = null,
        ?OutputInterface $output = null
    ) {
        $this->input = $input ?? new ArgvInput();
        $this->output = $output ?? new ConsoleOutput();
    }

    /**
     * {@inheritdoc}
     */
    public function createLogger(): LoggerInterface
    {
        return new ConsoleLogger($this->output);
    }

    /**
     * {@inheritdoc}
     */
    public function createContainer(): Container
    {
        return ContainerFactory::create();
    }

    /**
     * Get directory for commands
     * @return string
     */
    abstract protected function getCommandDirectory(): string;

    /**
     * {@inheritdoc}
     */
    final public function run(): void
    {
        $logger = $this->createLogger();
        $errorHandler = (new ErrorHandler($logger));
        $errorHandler->setErrorHandler();

        $container = $this->createContainer();
        $container->set(LoggerInterface::class, $logger);
        $container->set(ContainerInterface::class, $container);

        $app = new ApplicationBase(static::APP_NAME, static::APP_VERSION);
        $app->useContainer($container, true, true);
        $commands = CollectCommands::collect($this->getCommandDirectory());
        foreach ($commands as $command) {
            $app->command($command->getDefinition(), $command->getFunc());
        }

        try {
            $app->run($this->input, $this->output);
        } catch (Throwable $exception) {
            $logger->error($exception->getMessage(), compact('exception'));
            exit(1);
        }

        $errorHandler->restoreErrorHandler();
        exit(0);
    }
}
