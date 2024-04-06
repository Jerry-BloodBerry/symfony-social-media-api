<?php

namespace App\Post\Application\Query\Message;

use App\Common\CQRS\QueryInterface;
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
