<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Edge\User;

use Bleeding\Exceptions\RuntimeException;

use function password_hash;

use const PASSWORD_DEFAULT;

/**
 * @package Edge\User
 */
final class LoginService
{
    /**
     * @param string $username
     * @param string $rawPassword
     * @return User
     */
    public function __invoke(string $username, string $rawPassword): UserEntity
    {
        $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

        if ($username !== 'test') {
            throw RuntimeException::create('ユーザ名またはパスワードが間違っています', 1);
        } elseif (!password_verify($rawPassword, '$2y$10$R0oLxUu4tenpPWdeGyYELeEoO5SOTMSY7sNQ723aYXVd0uC.l4SEe')) {
            throw RuntimeException::create('ユーザ名またはパスワードが間違っています', 1);
        }

        return new UserEntity($username, $hashedPassword);
    }
}
