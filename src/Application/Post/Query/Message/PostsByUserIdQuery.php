<?php

namespace App\Application\Post\Query\Message;

use App\Application\Service\CQRS\QueryInterface;
use Ramsey\Uuid\UuidInterface;

final class PostsByUserIdQuery implements QueryInterface
{
  public function __construct(
    public readonly UuidInterface $id,
    public readonly int $limit,
    public readonly int $offset
  ) {
  }
}
