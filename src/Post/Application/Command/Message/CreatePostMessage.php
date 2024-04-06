<?php

namespace App\Post\Application\Command\Message;

use App\Common\CQRS\CommandInterface;
use Ramsey\Uuid\UuidInterface;

class CreatePostMessage implements CommandInterface
{
  public function __construct(
    public readonly UuidInterface $id,
    public readonly UuidInterface $authorId,
    public readonly string $content,
    public readonly \DateTimeImmutable $createdAt
  ) {

  }
}
