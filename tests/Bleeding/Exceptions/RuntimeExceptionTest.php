<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Exceptions;

use Bleeding\Exceptions\RuntimeException;
use RuntimeException as RuntimeExceptionBase;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Exceptions
 * @coversDefaultClass RuntimeException
 */
final class RuntimeExceptionTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $message = 'test';
        $code = 200;
        $previous = new RuntimeExceptionBase('foo');
        $e = new RuntimeException($message, $code, $previous);

        $this->assertInstanceOf(RuntimeException::class, $e);
        $this->assertInstanceOf(RuntimeExceptionBase::class, $e);
    }

    /**
     * @test
     * @covers ::getContext
     */
    public function testGetContext()
    {
        $e = new RuntimeException('test', 200);
        $this->assertEquals([], $e->getContext());
    }

    /**
     * @test
     * @covers ::create
     * @covers ::setContext
     */
    public function testCreate()
    {
        $e = RuntimeException::create('message', 100, ['context' => 'is fine']);

        $this->assertInstanceOf(RuntimeException::class, $e);
        $this->assertEquals(['context' => 'is fine'], $e->getContext());
    }
}
