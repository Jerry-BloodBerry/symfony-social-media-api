<?php

namespace App\User\Application\Command\Message;

use App\Common\CQRS\CommandInterface;
use Ramsey\Uuid\UuidInterface;

class DeleteUserMessage implements CommandInterface
{
    public function __construct(
        public readonly UuidInterface $id
    ) {
    }
}