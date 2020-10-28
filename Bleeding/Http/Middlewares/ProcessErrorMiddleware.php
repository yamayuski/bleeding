<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Bleeding\Http\Middlewares;

use Bleeding\Exceptions\RuntimeException;
use Bleeding\Http\Exceptions\MethodNotAllowedException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

use function array_map;
use function getenv;
use function get_debug_type;
use function implode;
use function json_encode;
use function strlen;

use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_UNICODE;

/**
 * Process Runtime Error
 * @package Bleeding\Http\Middlewares
 */
final class ProcessErrorMiddleware implements MiddlewareInterface
{
    /**
     * constructor
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private LoggerInterface $logger
    ) {}

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $throwable) {
            return $this->processThrowable($request, $throwable);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param Throwable $throwable
     * @return ResponseInterface
     */
    private function processThrowable(ServerRequestInterface $request, Throwable $throwable): ResponseInterface
    {
        $code = $throwable->getCode();
        $response = $this->responseFactory->createResponse(($code > 100 && $code < 600) ? (int)$code : 500);
        $backtrace = array_map(fn (array $arg) =>
            sprintf("%s%s%s() in %s:%s",
                $arg['class'] ?? '',
                $arg['type'] ?? '',
                $arg['function'] ?? '',
                $arg['file'] ?? '',
                $arg['line'] ?? ''
            ),
            $throwable->getTrace()
        );
        $body = [
            'type' => get_debug_type($throwable),
            'message' => $throwable->getMessage(),
            'code' => $throwable->getCode(),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'context' => $throwable instanceof RuntimeException ? $throwable->getContext() : [],
            'previous' => $throwable->getPrevious(),
            'trace' => $backtrace,
        ];

        $this->logger->error($throwable->getMessage(), $body);
        if (getenv('DEBUG_MODE') !== 'true') {
            $body = ['message' => 'Internal Server Error'];
        }
        $bodyRaw = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $response->getBody()->write($bodyRaw);
        if ($throwable instanceof MethodNotAllowedException) {
            $response = $response->withHeader('Allow', implode(',', $throwable->getContext()['allow']));
        }
        return $response->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->withHeader('Content-Length', (string)strlen($bodyRaw));
    }
}
