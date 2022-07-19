<?php
declare(strict_types = 1);

namespace Tigloo\Core;

use Tigloo\Container\Contracts\ContainerInterface;
use Tigloo\Collection\Contracts\CollectionInterface;
use Tigloo\Collection\Collection;
use DirectoryIterator;
use RuntimeException;

final class FileSystem
{
    private ContainerInterface $app;

    private CollectionInterface $factory;

    public function __construct(ContainerInterface $app)
    {
        $this->app = $app;
        $this->factory = new Collection();
    }
    
    public function load(string $path): FileSystem
    {
        if (null !== $path || ! file_exists($path)) {
            throw new RuntimeException();
        }

        if (is_file($path)) {
            $collection = new Collection($this->openFile($path));
        } elseif (is_dir($path)) {
            $collection = new Collection($this->openDirectory($path));
        }
        
        $this->factory->merge($collection);
        return $this;
    }

    public function output(): CollectionInterface
    {
        return $this->factory;
    }

    private function openFile(string $file): array
    {
        switch (pathinfo($file)['extension']) {
            case 'php':
                return @require $file;
        }
    }

    private function openDirectory(string $directory): array
    {
        foreach (new DirectoryIterator($directory) as $item) {
            if (! $item->isDot()) {
                if ($item->isDir()) {
                    $buffer[] = $this->openDirectory($item->getPathname());
                } else {
                    $buffer[] = $this->openFile($item->getPathname());
                }
            }
        }

        return $buffer ?? [];
    }
}