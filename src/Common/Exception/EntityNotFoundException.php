<?php

namespace App\Common\Exception;

class EntityNotFoundException extends \RuntimeException
{
    public function __construct(string $message = 'Entity not found.', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}