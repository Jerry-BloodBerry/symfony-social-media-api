<?php

namespace App\User\Application\Query\Message;

use App\Common\CQRS\QueryInterface;
use Ramsey\Uuid\UuidInterface;

final class UserByIdQuery implements QueryInterface
{
  public function __construct(
    public readonly UuidInterface $id
  ) {
  }
}
