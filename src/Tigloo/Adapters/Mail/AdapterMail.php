<?php
declare(strict_types = 1);

namespace Tigloo\Adapters\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Tigloo\Collection\Collection;

final class AdapterMail
{
    private PHPMailer $mailer;
    private Collection $recipients;
    private array $errors = [];

    public function __construct(private array $options = [])
    {
        $this->mailer = new PHPMailer();
        $this->recipients = new Collection();
        $this->mailer->isSMTP(); // par défault..
        $this->mailer->SMTPAuth = true; // par défault..

        // connection
        $this->setHost($this->options['host'] ?? null);
        $this->setPort($this->options['port'] ?? 587);
        $this->setUsername($this->options['username'] ?? null);
        $this->setPassword($this->options['password'] ?? null);
        $this->setEncryption($this->options['encryption'] ?? 'tls');
    }

    public function setHost(?string $host): AdapterMail
    {
        if ($host !== null) {
            $this->mailer->Host = $host;
        }
        return $this;
    }

    public function getHost(): string
    {
        return $this->mailer->Host;
    }

    public function setPort(int $port): AdapterMail
    {
        $this->mailer->Port = $port;
        return $this;
    }

    public function getPort(): int
    {
        return (int) $this->mailer->Port;
    }

    public function setUsername(?string $username): AdapterMail
    {
        if ($username !== null) {
            $this->mailer->Username = $username;
        }
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->mailer->Username;
    }

    public function setPassword(?string $password): AdapterMail
    {
        if ($password !== null) {
            $this->mailer->Password = $password;
        }
        return $this;
    }

    public function setEncryption(string $encryption = 'tls'): AdapterMail
    {
        $this->mailer->SMTPSecure = $encryption;
        return $this;
    }

    public function getEncryption(): string
    {
        return $this->mailer->SMTPSecure;
    }

    public function setConnectionAlive(bool $alive = false): AdapterMail
    {
        $this->mailer->SMTPKeepAlive = $alive;
        return $this;
    }

    public function getConnectionAlive(): bool
    {
        return $this->mailer->SMTPKeepAlive;
    }

    public function addTo(string $to, ?string $name = null): AdapterMail
    {
        $this->recipients->add($to, $name ?? '');
        return $this;
    }

    public function setReplyTo(string $to, ?string $name = null): AdapterMail
    {
        $this->mailer->addReplyTo($to, $name ?? '');
        return $this;
    }

    public function setFrom(string $from, ?string $name = null): AdapterMail
    {
        $this->mailer->setFrom($from, $name);
        return $this;
    }

    public function getFrom(): array
    {
       return [
            'from' => $this->mailer->From,
            'name' => $this->mailer->FromName
       ];
    }

    public function setSubject(?string $subject): AdapterMail
    {
        if ($subject !== null) {
            $this->mailer->Subject = $subject;
        }
        return $this;
    }

    public function getSubject(): string
    {
        return $this->mailer->Subject;
    }

    public function setBody(
        string $body, 
        bool $isHtml = false, 
        ...$attachmentImages
    ): AdapterMail
    {
        $this->mailer->isHTML($isHtml);
        if (! $isHtml && $this->mailer->Body !== '') {
            $this->mailer->AltBody = $body;
        } else {
            $this->mailer->Body = $body;
        }
        
        foreach ($attachmentImages as $attachment) {
            $infoImage = @getimagesize($attachment);
            if (file_exists($attachment) && @is_array($infoImage)) {
                $this->mailer->addEmbeddedImage(
                    $attachment, 
                    $this->camelCase(pathinfo($attachment)['filename']), 
                    pathinfo($attachment)['basename'], 
                    $this->mailer::ENCODING_BASE64, 
                    $infoImage['mime']
                );
            }
        }

        return $this;
    }

    public function getBody(): string
    {
        return $this->mailer->Body;
    }

    public function setAttachmentFile(...$files): AdapterMail
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                $this->mailer->addAttachment($file, pathinfo($file)['basename']);
            }
        }
        return $this;
    }

    public function send(): void
    {
        $it = $this->recipients->getIterator();
            $it->rewind();
            while($it->valid()) {
                try {
                    $this->mailer->addAddress($it->key(), $it->current());
                    $this->mailer->send();
                    $this->mailer->clearAddresses();
                    $this->errors[$it->key()] = $this->mailer->ErrorInfo;
                } catch (Exception $e) {
                    $this->errors[$it->key()] = $e->getMessage();
                }
                $it->next();
            }

            if ($this->getConnectionAlive()) {
                $this->mailer->smtpClose();
            }
            return;
    }

    public function errorsInfo(): array
    {
        return $this->errors;
    }

    private function camelCase(string $str): string
    {
        return str_replace(
            ' ',
            '',
            ucwords(str_replace(array('-', '_'), ' ', $str))
        );
    }
}