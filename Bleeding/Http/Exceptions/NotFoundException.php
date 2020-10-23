<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Http\Exceptions;

/**
 * 404 NotFound HTTP Exception
 * @package Bleeding\Http\Exceptions
 */
final class NotFoundException extends BadRequestException
{
    /** {@inheritdoc} */
    protected const MESSAGE = 'Not Found';

    /** @var int Exception code */
    protected const CODE = 404;
}
