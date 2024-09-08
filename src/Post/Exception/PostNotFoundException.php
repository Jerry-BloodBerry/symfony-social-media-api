<?php
use App\Common\Exception\EntityNotFoundException;
use Ramsey\Uuid\UuidInterface;

class PostNotFoundException extends EntityNotFoundException
{

    public function __construct(UuidInterface $postId)
    {
        parent::__construct(sprintf('Post with ID %s not found.', $postId->toString()));
    }
}