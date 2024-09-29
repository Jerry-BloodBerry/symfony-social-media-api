<?php

namespace App\Post\Application\Event;

use Ramsey\Uuid\UuidInterface;

class PostUpdatedEvent
{
    const NAME = 'integration.post.updated';
    public function __construct(
        public readonly UuidInterface $postId
    ) {
    }
}