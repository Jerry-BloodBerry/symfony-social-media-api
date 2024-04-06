<?php

namespace App\Post\Application\Query\Message;

use App\Common\CQRS\QueryInterface;

final class PostsQuery implements QueryInterface
{
  public function __construct(
    public readonly int $limit,
    public readonly int $offset
  ) {
  }
}
