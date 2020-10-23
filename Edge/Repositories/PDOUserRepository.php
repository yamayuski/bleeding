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
final class PDOUserRepository implements IUserRepository
{
    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?UserEntity
    {
        throw new \LogicException('Not implemented');
        // $sql = 'SELECT * FROM `users` WHERE `id` = :id;';
        // $stmt = $this->connection->user($id)->prepare($sql);
        // $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        // $stmt->execute();
        // $result = $stmt->fetch();
        // if (!$result) {
        //     return null;
        // }
        // return new UserEntity((int)$result['id'], $result['username'], $result['password']);

        // $result = $this->connection
        //     ->user($id)
        //     ->table('users')
        //     ->find($id);
        // if (!$result) {
        //     return null;
        // }
        // return new UserEntity((int)$result['id'], $result['username'], $result['password']);
    }

    /**
     * {@inheritdoc}
     */
    public function findByName(string $username): ?UserEntity
    {
        throw new \LogicException('Not implemented');
        // return $this->connection
        //     ->userAll()
        //     ->table('users')
        //     ->where('username', $username)
        //     ->first();
    }
}
