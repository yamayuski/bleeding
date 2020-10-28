<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Applications;

use Bleeding\Applications\ErrorHandler;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

use function trigger_error;

use const E_USER_NOTICE;

/**
 * @package Tests\Bleeding\Applications
 * @coversDefaultClass \Bleeding\Applications\ErrorHandler
 * @immutable
 */
final class ErrorHandlerTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::setErrorHandler
     * @covers ::restoreErrorHandler
     */
    public function testConstruct(): void
    {
        $errorHandler = new ErrorHandler(new Logger('ErrorHandlerTest::testConstruct'));
        $errorHandler->setErrorHandler();
        $errorHandler->restoreErrorHandler();

        $this->assertInstanceOf(ErrorHandler::class, $errorHandler);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::setErrorHandler
     * @covers ::restoreErrorHandler
     * @covers ::handle
     */
    public function testHandle(): void
    {
        $testHandler = new TestHandler();
        $logger = new Logger('ErrorHandlerTest::testHandle');
        $logger->pushHandler($testHandler);
        $errorHandler = new ErrorHandler($logger);

        $error = error_reporting();
        error_reporting(0);
        $errorHandler->setErrorHandler();
        trigger_error('Unknown Error', E_USER_NOTICE);
        $errorHandler->restoreErrorHandler();
        error_reporting($error);

        $this->assertTrue($testHandler->hasDebugThatContains('Unknown Error'));
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::setErrorHandler
     * @covers ::restoreErrorHandler
     * @covers ::handle
     */
    public function testSuppressed(): void
    {
        $testHandler = new TestHandler();
        $logger = new Logger('ErrorHandlerTest::testHandle');
        $logger->pushHandler($testHandler);
        $errorHandler = new ErrorHandler($logger);

        $errorHandler->setErrorHandler();
        @unlink(__DIR__ . DIRECTORY_SEPARATOR . 'foo');
        $errorHandler->restoreErrorHandler();

        $this->assertTrue($testHandler->hasErrorThatContains('No such file or directory'));
    }
}
