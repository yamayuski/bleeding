<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Support;

use Bleeding\Support\Clock;
use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosInterface;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Support
 * @coversDefaultClass \Bleeding\Support\Clock
 */
final class ClockTest extends TestCase
{
    /**
     * @return void
     */
    public function tearDown(): void
    {
        Chronos::setTestNow();
    }

    /**
     * @test
     * @covers ::entry
     */
    public function testEntry(): void
    {
        $instance = Clock::entry();

        $this->assertInstanceOf(ChronosInterface::class, $instance);
        $this->assertEquals('1970-01-01T00:00:00+00:00', $instance->toIso8601String());
    }

    /**
     * @test
     * @covers ::now
     */
    public function testNow(): void
    {
        $str = '2020-01-01T09:00:00+09:00';
        Chronos::setTestNow($str);
        $instance = Clock::now();

        $this->assertInstanceOf(ChronosInterface::class, $instance);
        $this->assertEquals($str, $instance->toIso8601String());
    }
}
