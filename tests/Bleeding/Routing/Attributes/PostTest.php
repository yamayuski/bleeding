<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Routing\Attributes;

use Bleeding\Routing\Attributes\Post;
use Tests\TestCase;

/**
 * @package Tests\Bleeding\Routing\Attributes
 * @coversDefaultClass \Bleeding\Routing\Attributes\Post
 */
final class PostTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $attr = new Post('/');

        $this->assertInstanceOf(Post::class, $attr);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getPath
     */
    public function testPostPath(): void
    {
        $attr = new Post('foo/bar/');

        $this->assertSame('/foo/bar', $attr->getPath());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getMethodName
     */
    public function testPostMethodName(): void
    {
        $attr = new Post('');

        $this->assertSame('POST', $attr->getMethodName());
    }
}
