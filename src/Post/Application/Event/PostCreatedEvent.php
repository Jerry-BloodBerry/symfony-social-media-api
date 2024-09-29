<?php

namespace App\Post\Application\Event;

use Ramsey\Uuid\UuidInterface;

class PostCreatedEvent
{
    const NAME = 'integration.post.created';
    public function __construct(
        public readonly UuidInterface $postId
    ) {
    }
}