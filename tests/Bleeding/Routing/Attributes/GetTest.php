<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Routing\Attributes;

use Bleeding\Routing\Attributes\Get;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Routing\Attributes
 * @coversDefaultClass \Bleeding\Routing\Attributes\Get
 */
final class GetTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $attr = new Get('/');

        $this->assertInstanceOf(Get::class, $attr);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getPath
     */
    public function testGetPath(): void
    {
        $attr = new Get('foo/bar/');

        $this->assertSame('/foo/bar', $attr->getPath());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getMethodName
     */
    public function testGetMethodName(): void
    {
        $attr = new Get('');

        $this->assertSame('GET', $attr->getMethodName());
    }
}
