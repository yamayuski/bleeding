<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Edge\User;

use Bleeding\Exceptions\RuntimeException;
use DI\Attribute\Inject;

use function password_verify;

/**
 * @package Edge\User
 */
final class LoginService
{
    /**
     * @param IUserRepository $repo
     */
    public function __construct(
        private IUserRepository $repo
    ) {}

    /**
     * @param string $username
     * @param string $rawPassword
     * @return UserEntity
     * @throws RuntimeException
     */
    public function __invoke(string $username, string $rawPassword): UserEntity
    {
        $user = $this->repo->findByName($username);

        if (is_null($user)) {
            throw RuntimeException::create('ユーザ名またはパスワードが間違っています', 400);
        }

        if (!password_verify($rawPassword, $user->getHashedPassword())) {
            throw RuntimeException::create('ユーザ名またはパスワードが間違っています', 400);
        }

        return $user;
    }
}
