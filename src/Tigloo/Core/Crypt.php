<?php
declare(strict_types = 1);

namespace Tigloo\Core;

use RuntimeException;

final class Crypt
{
    private string $algo = 'sha256';
    private int $length = 16;

    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 13]);
    }

    public function validatePassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function encrypt(string $data, string $secret)
    {
        $keySalt = substr(bin2hex(random_bytes($this->length)), 0, ($this->length * 2));
        $key = hash_hkdf($this->algo, $secret, $this->length, '', $keySalt);
        $iv = random_bytes($this->length);
        $encrypted = openssl_encrypt($data, 'AES-128-CBC', $key, 0, $iv);
        $keyAuth = hash_hkdf($this->algo, $key, $this->length, $secret);
        $signed = $this->sign($iv . $encrypted, $keyAuth);
        return $keySalt . $signed;
    }

    public function decrypt(string $data, string $secret): string
    {
        $keySalt = $this->substring($data, 0, ($this->length * 2));
        $key = hash_hkdf($this->algo, $secret, $this->length, '', $keySalt);
        $keyAuth = hash_hkdf($this->algo, $key, $this->length, $secret);
        $data = $this->design($this->substring($data, ($this->length * 2)), $keyAuth);
        $iv = $this->substring($data, 0, $this->length);
        $encrypted = $this->substring($data, $this->length);
        return openssl_decrypt($encrypted, 'AES-128-CBC', $key, 0, $iv);
    }

    public function sign(string $data, string $auth): string
    {
        $signed = hash_hmac($this->algo, $data, $auth, false);
        return $signed . $data;
    }

    public function design(string $data, string $auth): ?string
    {
        $test = hash_hmac($this->algo, '', '', false);
        if (! $test) {
            return new RuntimeException('Impossible de générer une clé de cryptage avec l\'algorithme', 500);
        }
        $hashTested = $this->lenstring($test);
        if ($this->lenstring($data) >= $hashTested) {
            $hash = $this->substring($data, 0, $hashTested);
            $hashValue = $this->substring($data, $hashTested, null);
            $calculated = hash_hmac($this->algo, $hashValue, $auth, false);

            if (hash_equals($hash, $calculated)) {
                return $hashValue;
            }
        }
        return null;
    }

    private function substring(string $data, int $start, int $end = null)
    {
        return mb_substr($data, $start, $end ?? $this->lenstring($data), '8bit');
    }

    private function lenstring(string $data): int
    {
        return mb_strlen($data, '8bit');
    }
}