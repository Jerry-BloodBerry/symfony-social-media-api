<?php

namespace App\Post\Domain;

use App\Common\DomainEvent;
use Ramsey\Uuid\UuidInterface;

class PostUpdatedDomainEvent extends DomainEvent
{
    public const NAME = 'post.updated';
    public readonly UuidInterface $postId;
    public function __construct(
        UuidInterface $postId
    ) {
        parent::__construct(self::NAME);
        $this->postId = $postId;
    }
}