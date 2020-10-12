<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Http\Middlewares;

use Bleeding\Exceptions\RuntimeException;
use Bleeding\Http\Exceptions\HttpClientException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Riverline\MultiPartParser\Converters\PSR7;

use function json_decode;
use function parse_str;
use function str_ends_with;
use function str_starts_with;

/**
 * Parse request body
 * @package Bleeding\Http\Middlewares
 */
final class AwareContentLengthMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $size = $response->getBody()->getSize();
        if (!is_null($size) && !$response->hasHeader('Content-Length')) {
            return $response->withHeader('Content-Length', (string)$size);
        }
        return $response;
    }
}
