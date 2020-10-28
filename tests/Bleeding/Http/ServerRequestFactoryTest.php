<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Http;

use Bleeding\Http\ServerRequestFactory;
use Bleeding\Http\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Http
 * @coversDefaultClass \Bleeding\Http\ServerRequestFactory
 */
final class ServerRequestFactoryTest extends TestCase
{
    /**
     * @test
     * @covers ::createFromGlobals
     */
    public function testCreateFromGlobals(): void
    {
        $factory = new ServerRequestFactory();
        $serverRequest = $factory->createFromGlobals();

        $this->assertInstanceOf(ServerRequestFactoryInterface::class, $factory);
        $this->assertInstanceOf(ServerRequestInterface::class, $serverRequest);
    }
}
