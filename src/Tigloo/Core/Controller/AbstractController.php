<?php
declare(strict_types = 1);

namespace Tigloo\Core\Controller;

use Tigloo\Container\Contracts\ContainerInterface;

abstract class AbstractController
{
    private ContainerInterface $app;

    public function setContainer(ContainerInterface $app): void
    {
        $this->app = $app;
    }
}