<?php

namespace App\Application\User\Command\Message;

use App\Application\Service\CQRS\CommandInterface;
use Ramsey\Uuid\UuidInterface;

class CreateUserMessage implements CommandInterface
{
  public function __construct(
    public readonly UuidInterface $id,
    public readonly string $username,
    public readonly string $email,
    public readonly string $avatarUrl,
    public readonly \DateTime $createdAt
  ) {

  }
}
