<?php

namespace App\Application\User\Query\Message;

use App\Application\Service\CQRS\QueryInterface;
use Ramsey\Uuid\UuidInterface;

final class UserByIdQuery implements QueryInterface
{
  public function __construct(
    public readonly UuidInterface $id
  ) {
  }
}
