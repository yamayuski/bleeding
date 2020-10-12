<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

// autoload
require implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'vendor', 'autoload.php']);

use Bleeding\WebApplication;
use Edge\Bootstrap\ContainerFactory;
use Psr\Container\ContainerInterface;

// run application
$app = new class extends WebApplication {
    public function createContainer(): ContainerInterface
    {
        return ContainerFactory::create();
    }
};

$app->run();
