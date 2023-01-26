<?php
declare(strict_types = 1);

namespace Tigloo\Adapters\Mail;

use Laminas\Mime\Message;
use Laminas\Mime\Part;
use Laminas\Mime\Mime;
use Tigloo\Collection\Collection;

class AdapterBodyMail
{
    private Collection $parts;

    public function __construct()
    {
        $this->parts = new Collection();
    }

    public function setHtml(string $content): AdapterBodyMail
    {
        $part = new Part($content);
        $part->charset = 'utf-8';
        $part->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
        $part->type = Mime::TYPE_HTML;
        $this->parts->add(Mime::TYPE_HTML, $part);
        return $this;
    }

    public function setAttachments(string $path): AdapterBodyMail
    {
        if ($this->parts->has('attachments')) {
            $this->parts->add('attachments', new Collection());
        }

        if (file_exists($path)) {
            $image = new Part(@fopen($path, 'r'));
            $image->type = getimagesize($path)['mime']; 
            $image->filename = pathinfo($path)['filename'];
            $image->disposition = Mime::DISPOSITION_ATTACHMENT;
            $image->encoding = Mime::ENCODING_BASE64;
            $this->parts->attachments->add('1', $image);
        }

        return $this;
    }

    public function toMessage(): Message 
    {
        $parts = $this->parts->has(Mime::TYPE_HTML) ? [$this->parts->get(Mime::TYPE_HTML)] : [];
        if ($this->parts->has('attachments')) {
            $it = $this->parts->attachments->getIterator();
            $it->rewind();
            while($it->valid()) {
                array_push($parts, $it->current());
                $it->next();
            }
        }
        return (new Message())->setParts($parts);
    }
}