<?php

namespace App\User\Exception;

class DuplicateEmailsNotAllowedException extends \Exception
{
    public function __construct(string $message = "", $code = 0, \Throwable|null $previous = null)
    {
        parent::__construct($message ?: 'Duplicate user emails are not allowed.', $code, $previous);
    }
}