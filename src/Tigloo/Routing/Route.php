<?php
declare(strict_types = 1);

namespace Tigloo\Routing;

final class Route
{
    private ?string $name = null;
    
    private string $method;
    
    private string $pattern;
    
    private object|string $action;

    private ?object $event;

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

    public function getEvent(): ?object
    {
        return $this->event;
    }

    public function hasEvent(): bool
    {
       return isset($this->event);
    }

    public function withEvent(?string $event): Route
    {
        if ($event !== null && class_exists($event)) {
            $this->event = $event;
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