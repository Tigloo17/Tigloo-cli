<?php
declare(strict_types = 1);

namespace Tigloo\Adapters\Mail;

use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp;
use Tigloo\Collection\Collection;

final class AdapterMail
{
    private Message $message;
    private Smtp $transport;
    private Collection $recipients;

    public function __construct(private AdapterSmtpMail $options)
    {
        $this->message = new Message();
        $this->transport = new Smtp($this->options->getOptions());
        $this->recipients = new Collection();
    }

    public function setTo(string|array $to, ?string $name = null): AdapterMail
    {
        if (is_string($to)) {
            $to = [($name ?? 0) => $to];
        }

        foreach ($to as $key => $address) {
            $this->recipients->add($key, $address);
        }
        return $this;
    }

    public function setFrom(string $from, ?string $name = null): AdapterMail
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

    public function setBody(string|AdapterBodyMail $body): AdapterMail
    {
        if ($body instanceof AdapterBodyMail) {
            $body = $body->toMessage();
            $contentType = $this->message->getHeaders()->get('Content-type');
            $contentType->setType('multipart/related');
        }

        $this->message->setBody($body);
        return $this;
    }

    public function getBody()
    {
        return $this->message->getBody();
    }

    public function send()
    {
        $it = $this->recipients->getIterator();
        while($it->valid()) {
            $name = is_int((int) $it->key()) ? null : $it->key();
            $this->message->setTo($it->current(), $name);
            $this->transport->send($this->message);
            $it->next();
        }
    }
}