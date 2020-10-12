<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Http;

use Laminas\Diactoros\ServerRequestFactory as DiactorosServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package Bleeding\Http
 */
final class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromGlobals(): ServerRequestInterface
    {
        return DiactorosServerRequestFactory::fromGlobals();
    }
}
