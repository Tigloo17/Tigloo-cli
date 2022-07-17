<?php
declare(strict_types = 1);

namespace Tigloo\Container\Exceptions;

use Psr\Container\ContainerExceptionInterface;
use InvalidArgumentException;

class IdentifierException extends InvalidArgumentException implements ContainerExceptionInterface 
{
    
}