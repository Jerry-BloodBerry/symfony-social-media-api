<?php

namespace App\Post\Application\Command\Message;
use App\Common\CQRS\CommandInterface;
use Ramsey\Uuid\UuidInterface;

class DeletePostMessage implements CommandInterface
{
    public function __construct(
        public readonly UuidInterface $id
    ) {
    }
}