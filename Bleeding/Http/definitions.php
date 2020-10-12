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

use function DI\create;

return [
    ServerRequestFactoryInterface::class => create(ServerRequestFactory::class),
    ResponseFactoryInterface::class => create(ResponseFactory::class),
    StreamFactoryInterface::class => create(StreamFactory::class),
];
