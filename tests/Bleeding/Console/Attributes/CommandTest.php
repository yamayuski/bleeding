<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Console\Attributes;

use Bleeding\Console\Attributes\Command;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Console\Attributes
 * @coversDefaultClass \Bleeding\Console\Attributes\Command
 * @immutable
 */
final class CommandTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::getDefinition
     */
    public function testConstruct()
    {
        $command = new Command(
            $definition = 'hello'
        );

        $this->assertInstanceOf(Command::class, $command);
        $this->assertSame($definition, $command->getDefinition());
    }
}
