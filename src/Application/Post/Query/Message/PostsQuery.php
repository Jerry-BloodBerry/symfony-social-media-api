<?php

namespace App\Application\Post\Query\Message;

use App\Application\Service\CQRS\QueryInterface;

final class PostsQuery implements QueryInterface
{
  public function __construct(
    public readonly int $limit,
    public readonly int $offset
  ) {
  }
}
