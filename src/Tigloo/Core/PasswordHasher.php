<?php
declare(strict_types = 1);

namespace Tigloo\Core;

final class PasswordHasher
{
    private const ALGORITHM = [
        PASSWORD_BCRYPT => [
            'cost' => 13,
        ],
    ];

    public function hash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, self::ALGORITHM[PASSWORD_BCRYPT]);
    }

    public function validate(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}