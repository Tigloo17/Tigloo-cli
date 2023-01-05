<?php
declare(strict_types = 1);

namespace Tigloo\Routing;

final class Route
{
    private ?string $name = null;
    
    private string $method;
    
    private string $pattern;
    
    private object|string $action;

    private array $events = [];

    public function __construct(string $method, string $pattern, object|string $action)
    {
        $this->withMethod(strtoupper($method));
        $this->withPattern($pattern);
        $this->withAction($action);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function withName(string $name): Route
    {
        $this->name = $name;
        return $this;
    }

    public function getEvent(): array
    {
        return $this->events;
    }

    public function hasEvent(): bool
    {
       return empty($this->events);
    }

    public function withEvent(?array $events): Route
    {
        if ($events !== null) {
            foreach ($events as $event) {
                if (class_exists($event)) {
                    $this->events[] = $event;
                }
            }
        }
        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    protected function withMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    protected function withPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    public function getAction(): object|string
    {
        return $this->action;
    }

    protected function withAction(object|string $action): void
    {
        $this->action = $action;
    }
}