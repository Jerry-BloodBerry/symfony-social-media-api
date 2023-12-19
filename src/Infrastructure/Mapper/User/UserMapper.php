<?php

namespace App\Infrastructure\Mapper\User;

use App\Domain\User;
use App\Infrastructure\Mapper\User\UserMapperInterface;
use App\Infrastructure\Table\UsersTable;
use Ramsey\Uuid\Uuid;

class UserMapper implements UserMapperInterface
{
  public function toEntity(array $row): User
  {
    return User::restore(
      Uuid::fromString($row[UsersTable::ID]),
      $row[UsersTable::USERNAME],
      $row[UsersTable::EMAIL],
      $row[UsersTable::AVATAR_URL],
      new \DateTime($row[UsersTable::CREATED_AT]),
      isset($row[UsersTable::UPDATED_AT]) ? new \DateTime($row[UsersTable::UPDATED_AT]) : null,
    );
  }

  public function toArray(object $entity): array
  {
    if (!$entity instanceof User) {
      throw new \InvalidArgumentException('Entity should be of type User');
    }

    return [
      UsersTable::ID => $entity->getId()->toString(),
      UsersTable::USERNAME => $entity->getUsername(),
      UsersTable::EMAIL => $entity->getEmail(),
      UsersTable::AVATAR_URL => $entity->getAvatarUrl(),
      UsersTable::CREATED_AT => $entity->getCreatedAt()->format('Y-m-d H:i:s.v'),
      UsersTable::UPDATED_AT => $entity->getUpdatedAt() ? $entity->getUpdatedAt()->format('Y-m-d H:i:s.v') : null,
    ];
  }
}
