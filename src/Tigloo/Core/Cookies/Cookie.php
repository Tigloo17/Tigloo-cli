<?php
declare(strict_types = 1);

namespace Tigloo\Core\Cookies;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use RuntimeException;

final class Cookie
{
    protected $name;
    protected $value;
    protected $expires;
    protected $domain;
    protected $path;
    protected $secure;
    protected $httpOnly;
    protected $sameSite;

    public function __construct(
        string $name,
        string $value = '',
        ?DateTimeInterface $expires = null,
        ?string $domain = null,
        ?string $path = '/',
        ?bool $secure = true,
        ?bool $httpOnly = true,
        ?string $sameSite = 'Lax'
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->domain = $domain;
        $this->path = $path;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
        $this->setSameSite($sameSite);

        if ($expires) {
            $expires = $expires->setTimezone(new DateTimeZone('GMT'));
        }

        $this->expires = $expires ?? null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function withValue(string $value): self
    {
        $new = clone $this;
        $new->value = $value;
        return $new;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function withExpires(DateTimeInterface $dateTime): self
    {
        $new = clone $this;
        $new->expires = $dateTime->setTimezone(new DateTimeZone('GMT'));
        return $new;
    }

    public function getExpires(): ?DateTimeImmutable
    {
        if ($this->expires === null) {
            return null;
        }

        return (new DateTimeImmutable())->setTimestamp($this->expires->getTimestamp()) ?: null;
    }

    public function isExpired(): bool
    {
        return $this->expires !== null && $this->expires->getTimestamp() < time();
    }

    public function expire(): self
    {
        $new = clone $this;
        $new->expires = new DateTimeImmutable('-1 year');
        return $new;
    }

    public function withDomain(string $domain): self
    {
        $new = clone $this;
        $new->domain = $domain;
        return $new;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function withPath(string $path): self
    {
        $new = clone $this;
        $new->path = $path;
        return $new;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function withSecure(bool $secure = true): self
    {
        $new = clone $this;
        $new->secure = $secure;
        return $new;
    }

    public function isSecure(): bool
    {
        return $this->secure ?? false;
    }

    public function withHttpOnly(bool $httpOnly = true): self
    {
        $new = clone $this;
        $new->httpOnly = $httpOnly;
        return $new;
    }

    public function isHttpOnly(): bool
    {
        return $this->httpOnly ?? false;
    }

    public function withSameSite(string $sameSite): self
    {
        $new = clone $this;
        $new->setSameSite($sameSite);
        return $new;
    }

    public function getSameSite(): ?string
    {
        return $this->sameSite;
    }

    public function getOptions(): array
    {
        $options = [
            'expires' => '',
            'path' => $this->path,
            'domain' => $this->domain,
            'secure' => $this->secure,
            'httponly' => $this->httponly,
        ];

        if ($this->sameSite !== null) {
            $options['samesite'] = $this->sameSite;
        }

        return $options;
    }

    private function setSameSite(?string $sameSite): void
    {
        if (
            $sameSite !== null
            && !in_array($sameSite, ['Lax', 'Strict', 'None'], true)
        ) {
            Throw new RuntimeException('sameSite doit avoir comme valeur "Lax", "Strict" ou "None"', 500);
        }

        if ($sameSite === 'None') {
            $this->secure = true;
        }

        $this->sameSite = $sameSite;
    }
}