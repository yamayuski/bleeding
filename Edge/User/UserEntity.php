<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Edge\User;

use JsonSerializable;

/**
 * @package Edge\User
 */
final class UserEntity implements JsonSerializable
{
    /** @var string $username */
    private string $username;

    /** @var string $hashedPassword */
    private string $hashedPassword;

    /**
     * @param string $username
     * @param string $hashedPassword
     */
    public function __construct(string $username, string $hashedPassword)
    {
        $this->username = $username;
        $this->hashedPassword = $hashedPassword;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'username' => $this->username,
        ];
    }

    /**
     * @return string
     */
    public function getHashedPassword(): string
    {
        return $this->hashedPassword;
    }
}
