<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Http\Exceptions;

use Bleeding\Http\Exceptions\InternalServerErrorException;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Http\Exceptions
 * @coversDefaultClass \Bleeding\Http\Exceptions\InternalServerErrorException
 * @immutable
 */
final class InternalServerErrorExceptionTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::createWithMessage
     * @covers ::setContext
     * @covers ::getContext
     */
    public function testCreateWithMessage(): void
    {
        $exception = InternalServerErrorException::createWithMessage(
            $message = 'error',
            $context = ['foo' => 'bar'],
            $previous = null
        );

        $this->assertSame('error', $exception->getMessage());
        $this->assertSame(500, $exception->getCode());
        $this->assertSame(['foo' => 'bar'], $exception->getContext());
        $this->assertSame(null, $exception->getPrevious());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::createWithContext
     * @covers ::setContext
     * @covers ::getContext
     */
    public function testCreateWithContext(): void
    {
        $exception = InternalServerErrorException::createWithContext(
            $context = ['foo' => 'bar'],
            $previous = null
        );

        $this->assertSame('Internal Server Error', $exception->getMessage());
        $this->assertSame(['foo' => 'bar'], $exception->getContext());
        $this->assertSame(null, $exception->getPrevious());
    }
}
