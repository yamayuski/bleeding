<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

define('ENTRY_TIME', microtime(true));

// autoload
require implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'vendor', 'autoload.php']);

$exitCode = (new Edge\Applications\WebApplication)->run();

exit($exitCode);
