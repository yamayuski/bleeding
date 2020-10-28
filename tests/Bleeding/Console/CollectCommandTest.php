<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Console;

use Bleeding\Console\CollectCommand;
use Tests\TestCase;

use function implode;

use const DIRECTORY_SEPARATOR;

/**
 * @package Tests\Bleeding\Console
 * @coversDefaultClass \Bleeding\Console\CollectCommand
 * @immutable
 */
final class CollectCommandTest extends TestCase
{
    /**
     * @test
     * @covers ::collect
     * @covers ::checkFile
     * @uses \Bleeding\Console\Attributes\Command
     * @uses \Bleeding\Console\Command
     */
    public function testCollect()
    {
        $baseDir = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'Stub', 'Console']);
        $commands = CollectCommand::collect($baseDir);

        $this->assertIsArray($commands);
        $this->assertCount(2, $commands);
        $this->assertSame('hello', $commands[0]->getDefinition());
    }
}
