<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Http\Attributes;

use Bleeding\Http\Attributes\Middleware;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Http\Attributes
 * @coversDefaultClass \Bleeding\Http\Attributes\Middleware
 */
final class MiddlewareTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::getMiddlewareNames
     */
    public function testConstruct(): void
    {
        $attr = new Middleware([]);

        $this->assertInstanceOf(Middleware::class, $attr);
        $this->assertSame([], $attr->getMiddlewareNames());

        $attr = new Middleware('foo');

        $this->assertSame(['foo'], $attr->getMiddlewareNames());

        $attr = new Middleware(['foo', 'bar']);

        $this->assertSame(['foo', 'bar'], $attr->getMiddlewareNames());
    }
}
