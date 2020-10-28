<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Applications;

use Bleeding\Applications\LoggerFactory;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Applications
 * @coversDefaultClass \Bleeding\Applications\LoggerFactory
 * @immutable
 */
final class LoggerFactoryTest extends TestCase
{
    /**
     * @test
     * @covers ::create
     */
    public function testCreate(): void
    {
        $logger = LoggerFactory::create('Bleeding Test');

        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }
}
