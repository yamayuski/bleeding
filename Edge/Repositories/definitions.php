<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Edge\Repositories;

use Edge\User\IUserRepository;

use function DI\get;

return [
    IUserRepository::class => get(InMemoryUserRepository::class),
];
