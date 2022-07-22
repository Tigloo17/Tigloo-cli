<?php
declare(strict_types = 1);

namespace Tigloo\Core;

use Tigloo\Collection\Contracts\CollectionInterface;
use Tigloo\Collection\Collection;
use DirectoryIterator;
use RuntimeException;

final class FileSystem
{
    private CollectionInterface $factory;

    public function __construct()
    {
        $this->factory = new Collection();
    }
    
    public function load(string $path): FileSystem
    {
        if (null === $path || ! file_exists($path)) {
            throw new RuntimeException(sprintf('Le chemin d\'accès "%s" n\'existe pas', $path));
        }

        if (is_file($path)) {
            $import = $this->openFile($path);
        } elseif (is_dir($path)) {
            $import = $this->openDirectory($path); 
        }
        
        if (! empty($import)) {
            foreach ($import as $key => $value) {
                $collection = (is_numeric($key) && is_array($value)) 
                    ? new Collection($value) 
                    : new Collection($import);
                $this->factory = $this->factory->merge($collection);
            }
        }
        
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