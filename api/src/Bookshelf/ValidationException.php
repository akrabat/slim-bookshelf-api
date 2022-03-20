<?php

namespace App\Bookshelf;

use RuntimeException;
use Throwable;

class ValidationException extends RuntimeException
{
    protected array $errors = [];

    public function __construct(string $message = "", array $errors = [], ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
