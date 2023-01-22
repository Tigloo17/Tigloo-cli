<?php
declare(strict_types = 1);

namespace Tigloo\Adapters;

use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Message;

class AdapterMail
{
    protected SmtpOptions $options;
    protected Smtp $transport;
    protected Message $message;
    protected array $toSender;

    public function __construct(array $configs = [])
    {
        $options['name'] = $configs['host'] ?? 'localhost';
        $options['host'] = $configs['host'] ?? 'localhost';
        $options['port'] = $configs['port'] ?? '465';
        $options['connection_class'] = 'login';
        $options['connection_config'] = [
            'username' => $configs['username'] ?? '',
            'password' => $configs['password'] ?? '',
            'ssl' => 'ssl'
        ];
        $this->options = new SmtpOptions($options);
        $this->transport = new Smtp();
        $this->transport->setOptions($this->options);
        $this->message = new Message();
    }

    public function setTo(array|string $to, string $name = null): AdapterMail
    {
        $this->toSender = ! is_array($to) ? [$to => $name] : $to;
        return $this;
    }

    public function setFrom(string $from, string $name = null): AdapterMail
    {
        $this->message->addFrom($from, $name);
        return $this;
    }

    public function getFrom()
    {
        return $this->message->getFrom();
    }

    public function setSubject(string $subject): AdapterMail
    {
        $this->message->setSubject($subject);
        return $this;
    }

    public function getSubject()
    {
        return $this->message->getSubject();
    }

    public function setBody($body): AdapterMail
    {
        $this->message->setBody($body);
        return $this;
    }

    public function getBody()
    {
        return $this->message->getBody();
    }

    public function send()
    {
        foreach ($this->toSender as $to) {
            $this->message->setTo($to);
            $this->transport->send($this->message);
        }
    }
}