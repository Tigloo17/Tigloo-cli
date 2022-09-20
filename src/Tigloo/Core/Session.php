<?php
declare(strict_types = 1);

namespace Tigloo\Core;

final class Session
{
    public static function create(string $scheme = 'http')
    {
        if ($scheme === 'https' && ini_get('session.cookie_secure') != 1) {
            $config['init']['session.cookie_secure'] = 1;
        }

        return new static($config ?? []);
    }

    public function __construct(array $config = [])
    {
        $config += [
            'timeout' => null,
            'init' => []
        ];

        if ($config['timeout']) {
            $config['init']['session.gc_maxlifetime'] = 60 * $config['timeout'];
        }

        if (! isset($config['init']['session.cookie_path'])) {
            $cookiepath = empty($config['cookie_path']) ? '/' : $config['cookie_path'];
            $config['init']['session.cookie_path'] = $cookiepath;
        }

        $this->sendInitialization($config['init']);
        session_register_shutdown();
    }

    public function start(): bool
    {
        if ($this->isStarted()) {
            return false;
        }

        session_cache_limiter('nocache');
        if (session_start()) {
            session_regenerate_id();
            return true;
        }

        return false;
    }

    public function isStarted(): bool
    {
        if (function_exists('session_status')) {
            return session_status() === \PHP_SESSION_ACTIVE;
        }
        return false;
    }

    public function get(string $name, mixed $default = null): mixed
    {
        if ($this->has($name)) {
            return $_SESSION[$name];
        }

        return $default;
    }

    public function has(string $name): bool
    {
        if (! $this->isStarted()) {
            return false;
        }

        return isset($_SESSION[$name]);
    }

    public function set(string $name, mixed $value): void
    {
        if (! $this->isStarted()) {
            return;
        }

        $_SESSION[$name] = $value;
    }

    private function sendInitialization(array $configurations = []): void
    {
        if ($this->isStarted()) {
            return;
        }

        foreach ($configurations as $setting => $value) {
            if (ini_set($setting, (string) $value) === false) {
                throw new \RuntimeException(sprintf('Impossible de configurer la session avec le paramètre %s', $setting), 500);
            }
        }
    }
}