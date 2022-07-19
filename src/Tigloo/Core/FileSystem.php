<?php
declare(strict_types = 1);

namespace Tigloo\Core;

use Tigloo\Container\Contracts\ContainerInterface;

final class FileSystem
{
    private ContainerInterface $app;

    public function __construct(ContainerInterface $app)
    {
        $this->app = $app;
    }
}