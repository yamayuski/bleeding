<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Applications;

use Bleeding\Applications\WebApplication;
use DI\Container;
use Laminas\Diactoros\Response;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Applications
 * @coversDefaultClass \Bleeding\Applications\WebApplication
 * @immutable
 */
final class WebApplicationTest extends TestCase
{
    /**
     * @test
     * @covers ::createLogger
     * @covers ::createContainer
     * @covers ::run
     * @covers ::createServerRequest
     * @uses \Bleeding\Applications\ContainerFactory
     * @uses \Bleeding\Applications\LoggerFactory
     * @uses \Bleeding\Applications\ErrorHandler
     * @uses \Bleeding\Http\ServerRequestFactory
     * @uses \Bleeding\Routing\MiddlewareResolver
     */
    public function testRun(): void
    {
        $app = new class extends WebApplication {
            protected function createProcessQueue(Container $container): array {
                return [
                    fn () => new Response(),
                ];
            }
        };

        $exitCode = $app->run();

        $this->assertSame(0, $exitCode);
    }
}
