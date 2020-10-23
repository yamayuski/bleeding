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
    /** @var int $id */
    private int $id;

    /** @var string $username */
    private string $username;

    /** @var string $hashedPassword */
    private string $hashedPassword;

    /**
     * @param int $id
     * @param string $username
     * @param string $hashedPassword
     */
    public function __construct(int $id, string $username, string $hashedPassword)
    {
        $this->id = $id;
        $this->username = $username;
        $this->hashedPassword = $hashedPassword;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
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
