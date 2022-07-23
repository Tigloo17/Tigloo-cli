<?php
declare(strict_types = 1);

namespace Tigloo\Event;

use Throwable;

final class ErrorsEvent extends ResponseEvent
{
    private Throwable $errors;

    public function __construct(Throwable $e, $request)
    {
        parent::__construct($request);
        $this->errors = $e;
    }

    public function getThrowable(): Throwable
    {
        return $this->errors;
    }
}