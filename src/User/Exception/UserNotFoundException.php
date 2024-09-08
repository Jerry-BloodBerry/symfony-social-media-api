<?php

namespace App\User\Exception;

use App\Common\Exception\EntityNotFoundException;
use Ramsey\Uuid\UuidInterface;

class UserNotFoundException extends EntityNotFoundException
{
    public function __construct(UuidInterface $userId)
    {
        parent::__construct(sprintf('User with ID %s not found.', $userId->toString()));
    }
}