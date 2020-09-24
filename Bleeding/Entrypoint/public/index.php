<?php declare(strict_types=1);

/**
 * Entrypoint
 */

require_once implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', '..', 'vendor', 'autoload.php']);

use Laminas\Diactoros\ServerRequestFactory;
use Narrowspark\HttpEmitter\SapiEmitter;
use Relay\RelayBuilder;

use function Bleeding\Entrypoint\makeContainer;
use function Bleeding\Entrypoint\makeResolver;

$container = makeContainer();
$relayBuilder = new RelayBuilder(makeResolver($container));

/** @var callable[] $queue */
$queue = [];
$queue[] = $container->call(require implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'make200Handler.php']));
$queue[] = $container->call(require implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'make404Handler.php']));

// invoke
$request = ServerRequestFactory::fromGlobals();
$response = $relayBuilder
    ->newInstance($queue)
    ->handle($request);
(new SapiEmitter())->emit($response);