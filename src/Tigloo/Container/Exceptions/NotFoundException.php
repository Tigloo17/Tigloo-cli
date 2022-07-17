<?php
declare(strict_types = 1);

namespace Tigloo\Container\Exceptions;

use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('"%s" n\'est pas défini dans le container', $id));
    }
}