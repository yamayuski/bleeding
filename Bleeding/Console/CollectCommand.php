<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Console;

use Bleeding\Console\Attributes\Command as CommandAttr;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionFunction;
use SplFileInfo;

use function is_callable;
use function is_null;
use function str_ends_with;

/**
 * @package Bleeding\Console
 * @immutable
 */
final class CollectCommand
{
    /**
     * List up all routes
     *
     * @todo PHP file caching
     * @return array
     */
    public static function collect(string $baseDir): array
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($baseDir),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $commands = [];

        foreach ($iterator as $file) {
            $command = self::checkFile($file);

            if (!is_null($command)) {
                $commands[] = $command;
            }
        }

        return $commands;
    }

    /**
     * @internal
     * @param SplFileInfo $file
     * @return ?Command
     */
    private static function checkFile(SplFileInfo $file): ?Command
    {
        if (!str_ends_with($file->getBaseName(), '.php')) {
            return null;
        }

        $func = require $file->getRealPath();
        assert(is_callable($func), 'command is callable');

        /** @psalm-suppress InvalidArgument */
        $ref = new ReflectionFunction($func);
        assert(0 < count($ref->getAttributes()), 'command has attribute');

        $attr = null;
        if (0 < count($ref->getAttributes(CommandAttr::class))) {
            $attr = $ref->getAttributes(CommandAttr::class)[0]->newInstance();
        }
        assert(!is_null($attr), 'No command attribute has set: ' . $file->getRealPath());

        return new Command($attr->getDefinition(), $func);
    }
}
