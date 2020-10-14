<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Edge\Repositories;

use Edge\User\IUserRepository;
use Edge\User\UserEntity;

/**
 * @package Edge\Repositories
 */
final class InMemoryUserRepository implements IUserRepository
{
    /**
     * @param string $username
     * @return ?UserEntity
     */
    public function findByName(string $username): ?UserEntity
    {
        if ($username === 'test') {
            return new UserEntity($username, '$2y$10$R0oLxUu4tenpPWdeGyYELeEoO5SOTMSY7sNQ723aYXVd0uC.l4SEe');
        }
        return null;
    }
}
