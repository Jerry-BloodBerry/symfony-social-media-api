<?php

namespace App\Post\Domain;

use App\Common\DomainEvent;
use Ramsey\Uuid\UuidInterface;

class PostCreatedDomainEvent extends DomainEvent
{
    public const NAME = 'domain.post.created';
    public readonly UuidInterface $postId;
    public function __construct(
        UuidInterface $postId
    ) {
        parent::__construct(self::NAME);
        $this->postId = $postId;
    }
}