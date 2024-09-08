<?php

namespace App\Post\Application\Command\Message;

use App\Common\CQRS\CommandInterface;
use Ramsey\Uuid\UuidInterface;

class UpdatePostMessage implements CommandInterface
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly string $content
    ) {

    }
}