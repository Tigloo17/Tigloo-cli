<?php
declare(strict_types = 1);

namespace Tigloo\Adapters\Mail;

use Laminas\Mail\Transport\SmtpOptions;

class AdapterSmtpMail
{
    protected SmtpOptions $options;

    public function __construct(array $options = [])
    {
        $this->options = new SmtpOptions();
        
        $this->setName($options['name'] ?? null);
        $this->setHost($options['host'] ?? null);
        $this->setPort($options['port'] ?? 587);
        $this->setEncryption($options['encryption'] ?? 'tls');
        $this->setAuth($options['username'] ?? null, $options['password'] ?? null);
        $this->setLimit($options['time'] ?? null);
    }

    public function setName(?string $name): AdapterSmtpMail
    {
        if ($name !== null) {
            $this->options->setName($name);
        }
        return $this;
    }

    public function getName(): string
    {
        return $this->options->getName();
    }

    public function setHost(?string $host): AdapterSmtpMail
    {
        if ($host !== null) {
            $this->options->setHost($host);
        }
        return $this;
    }

    public function getHost(): string
    {
        return $this->options->getHost();
    }

    public function setPort(int $port): AdapterSmtpMail
    {
        $this->options->setPort($port);
        return $this;
    }

    public function getPort(): int
    {
        return $this->options->getPort();
    }

    public function setLimit(?int $second): AdapterSmtpMail
    {
        if ($second !== null) {
            $this->options->setConnectionTimeLimit($second);
        }
        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->options->getConnectionTimeLimit();
    }

    public function setAuth(?string $username, ?string $password): AdapterSmtpMail
    {
        if ($username !== null && $password !== null) {
            $this->options->setConnectionClass('login');
            $config = $this->options->getConnectionConfig();
            $config += ['username' => $username, 'password' => $password];
            $this->options->setConnectionConfig($config);
        } 
        return $this;
    }

    public function getAuth(): array
    {
        $config = $this->options->getConnectionConfig();
        return [
            'username' => $config['username'] ?? null,
            'password' => $config['password'] ?? null
        ];
    }

    public function setEncryption(?string $encryption = 'tls'): AdapterSmtpMail
    {
        $config = $this->options->getConnectionConfig();
        $config += ['ssl' => $encryption];
        $this->options->setConnectionConfig($config);
        return $this;
    }

    public function getEncryption(): ?string
    {
        return $this->options->getConnectionConfig()['ssl'] ?? null;
    }

    public function getOptions(): SmtpOptions
    {
        return $this->options;
    }
}