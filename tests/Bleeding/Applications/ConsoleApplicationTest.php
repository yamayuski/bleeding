<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Applications;

use Bleeding\Applications\ConsoleApplication;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Applications
 * @coversDefaultClass \Bleeding\Applications\ConsoleApplication
 * @immutable
 */
final class ConsoleApplicationTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::createLogger
     * @covers ::createContainer
     * @covers ::run
     * @uses \Bleeding\Applications\ContainerFactory
     * @uses \Bleeding\Applications\ErrorHandler
     * @uses \Bleeding\Console\Attributes\Command
     * @uses \Bleeding\Console\CollectCommand
     * @uses \Bleeding\Console\Command
     * @uses \Silly\Application
     */
    public function testRun(): void
    {
        $app = new class (new ArgvInput(['bleeding', 'hello']), new NullOutput()) extends ConsoleApplication {
            protected function getCommandDirectory(): string {
                return implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'Stub', 'Console']);
            }
        };

        $exitCode = $app->run();

        $this->assertSame(0, $exitCode);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::createLogger
     * @covers ::createContainer
     * @covers ::run
     * @uses \Bleeding\Applications\ContainerFactory
     * @uses \Bleeding\Applications\ErrorHandler
     * @uses \Bleeding\Console\Attributes\Command
     * @uses \Bleeding\Console\CollectCommand
     * @uses \Bleeding\Console\Command
     * @uses \Silly\Application
     */
    public function testRunFailed(): void
    {
        $app = new class (new ArgvInput(['bleeding', 'show-error']), new NullOutput()) extends ConsoleApplication {
            protected function getCommandDirectory(): string {
                return implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'Stub', 'Console']);
            }
        };

        $exitCode = $app->run();

        $this->assertSame(2, $exitCode);
    }
}
