<?php declare(strict_types=1);

/**
 * Entrypoint
 */

require implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', '..', 'vendor', 'autoload.php']);

use Laminas\Diactoros\ServerRequestFactory;
use Narrowspark\HttpEmitter\SapiEmitter;
use Relay\RelayBuilder;

use function Bleeding\Entrypoint\makeContainer;
use function Bleeding\Entrypoint\resolver;
use function Bleeding\Entrypoint\_404Handler;

// preprocess
$container = makeContainer();
$relayBuilder = new RelayBuilder(resolver($container));
$queue = [];
$queue[] = function ($request, $next) use ($container) {
    if ($request->getUri()->getPath() === '/') {
        $stream = $container->get(\Psr\Http\Message\StreamFactoryInterface::class)->createStream('Hello world');
        return $container->get(\Psr\Http\Message\ResponseFactoryInterface::class)
            ->createResponse(200, 'Ok')
            ->withBody($stream);
    }
    return $next($request);
};
$queue[] = _404Handler($container);
$relay = $relayBuilder->newInstance($queue);

// invoke
$request = ServerRequestFactory::fromGlobals();
$response = $relay->handle($request);
(new SapiEmitter())->emit($response);
