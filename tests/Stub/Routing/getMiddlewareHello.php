<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

use Bleeding\Http\Attributes\Middleware;
use Bleeding\Routing\Attributes\Get;

return
#[Get('/middleware')]
#[Middleware(\Tests\Stub\Middlewares\TestMiddleware::class)]
fn () => ['Hello' => 'world'];
