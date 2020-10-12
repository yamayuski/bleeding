<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

use Bleeding\Http\Attributes\Get;

return
#[Get('/')]
fn () => ['Hello' => 'world'];
