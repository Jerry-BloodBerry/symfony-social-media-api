<?php

namespace App\User\Exception;

class DuplicateUsernamesNotAllowedException extends \Exception
{
    public function __construct(string $message = "", $code = 0, \Throwable|null $previous = null)
    {
        parent::__construct($message ?: 'Duplicate user names are not allowed.', $code, $previous);
    }
}