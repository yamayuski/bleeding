<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Http\Exceptions;

/**
 * 400 Client HTTP Exception
 * @package Bleeding\Http\Exceptions
 */
class BadRequestException extends InternalServerErrorException
{
    /** {@inheritdoc} */
    protected const MESSAGE = 'Bad Request';

    /** {@inheritdoc} */
    protected const CODE = 400;
}
