<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Console;

use Bleeding\Console\Command;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Console
 * @coversDefaultClass \Bleeding\Console\Command
 * @immutable
 */
final class CommandTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::getDefinition
     * @covers ::getFunc
     */
    public function testConstruct()
    {
        $command = new Command(
            $definition = 'hello',
            $func = fn () => true
        );

        $this->assertInstanceOf(Command::class, $command);
        $this->assertSame($definition, $command->getDefinition());
        $this->assertSame($func, $command->getFunc());
    }
}
