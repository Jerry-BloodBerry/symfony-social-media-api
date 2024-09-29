<?php

namespace App\Post\Exception;

use App\Common\Exception\EntityNotFoundException;
use Ramsey\Uuid\UuidInterface;

class AuthorNotFoundException extends EntityNotFoundException
{

    public function __construct(UuidInterface $authorId)
    {
        parent::__construct(sprintf('Author with ID %s not found.', $authorId->toString()));
    }
}