<?php

namespace App\Application\Post\Query\Message;

use App\Application\Service\CQRS\QueryInterface;
use Ramsey\Uuid\UuidInterface;

final class PostByIdQuery implements QueryInterface
{
  public function __construct(
    public readonly UuidInterface $id
  ) {
  }
}
