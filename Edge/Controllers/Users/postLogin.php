<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

use Bleeding\Http\Attributes\Post;
use Bleeding\Http\Exceptions\HttpClientException;
use Edge\User\LoginService;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

return
#[Post('/users/login')]
function (ServerRequestInterface $request, LoginService $loginService) {
    $body = $request->getParsedBody();
    $username = $body['username'] ?? null;
    $rawPassword = $body['password'] ?? null;

    $usernameValidator = v::alnum()->noWhitespace()->length(3, 20);
    $rawPasswordValidator = v::alnum()->noWhitespace()->length(6, 64);

    if (!$usernameValidator->validate($username)) {
        throw HttpClientException::createWithoutCode('ユーザ名は半角英数 3 ～ 20 文字で入力してください');
    } elseif (!$rawPasswordValidator->validate($rawPassword)) {
        throw HttpClientException::createWithoutCode('パスワードは半角英数 6 ～ 64 文字で入力してください');
    }

    /**
     * @psalm-suppress PossiblyNullArgument
     */
    return $loginService($username, $rawPassword);
};
