<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Edge\Applications;

use Bleeding\Applications\ConsoleApplication as ConsoleApplicationBase;

use DI\Container;

/**
 * @package Edge\Applications
 */
class ConsoleApplication extends ConsoleApplicationBase {
    /**
     * {@inheritdoc}
     */
    public function createContainer(): Container
    {
        return ContainerFactory::create();
    }
};
