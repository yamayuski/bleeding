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
     * {@inheritdoc}
     */
    public function findById(int $id): ?UserEntity
    {
        if ($id === 1) {
            return new UserEntity($id, 'test', '$2y$10$R0oLxUu4tenpPWdeGyYELeEoO5SOTMSY7sNQ723aYXVd0uC.l4SEe');
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findByName(string $username): ?UserEntity
    {
        if ($username === 'test') {
            return new UserEntity(1, $username, '$2y$10$R0oLxUu4tenpPWdeGyYELeEoO5SOTMSY7sNQ723aYXVd0uC.l4SEe');
        }
        return null;
    }
}
