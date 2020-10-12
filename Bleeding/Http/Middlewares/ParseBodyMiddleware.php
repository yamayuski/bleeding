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
final class ParseBodyMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if (str_ends_with($contentType, 'json')) {
            $request = $this->parseJson($request);
        } elseif (str_starts_with($contentType, 'multipart')) {
            $request = $this->parseMultipart($request);
        } elseif (str_ends_with($contentType, 'x-www-form-urlencoded')) {
            $request = $this->parseForm($request);
        }
        return $handler->handle($request);
    }

    /**
     * Parse JSON body
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    protected function parseJson(ServerRequestInterface $request): ServerRequestInterface
    {
        try {
            return $request->withParsedBody(json_decode((string)$request->getBody(), true));
        } catch (RuntimeException $exception) {
            throw HttpClientException::createWithoutCode('Invalid json', [], $exception);
        }
    }

    /**
     * Parse multipart/form-data body
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    protected function parseMultipart(ServerRequestInterface $request): ServerRequestInterface
    {
        return $request->withParsedBody(PSR7::convert($request));
    }

    /**
     * Parse application/x-www-form-urlencoded
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    protected function parseForm(ServerRequestInterface $request): ServerRequestInterface
    {
        $data = [];
        parse_str((string)$request->getBody(), $data);
        return $request->withParsedBody($data);
    }
}
