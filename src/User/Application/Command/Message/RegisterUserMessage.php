<?php

namespace App\User\Application\Command\Message;

use App\Common\CQRS\CommandInterface;
use Ramsey\Uuid\UuidInterface;

class RegisterUserMessage implements CommandInterface
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
