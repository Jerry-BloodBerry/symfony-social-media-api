<?php

namespace App\Post\Application\Query\Message;

use App\Common\CQRS\QueryInterface;
use Ramsey\Uuid\UuidInterface;

final class PostByIdQuery implements QueryInterface
{
  public function __construct(
    public readonly UuidInterface $id
  ) {
  }
}
