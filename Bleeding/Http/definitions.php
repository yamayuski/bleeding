<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Http;

use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\StreamFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

use function DI\get;

return [
    ServerRequestFactoryInterface::class => get(ServerRequestFactory::class),
    ResponseFactoryInterface::class => get(ResponseFactory::class),
    StreamFactoryInterface::class => get(StreamFactory::class),
];
