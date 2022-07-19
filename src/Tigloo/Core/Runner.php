<?php
declare(strict_types = 1);

namespace Tigloo\Core;

use Tigloo\Core\Contracts\EventDispatcherInterface;

final class Runner
{
    protected EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle()
    {
        try {

        } catch (\Exception $e) {
            
        }
    }
}