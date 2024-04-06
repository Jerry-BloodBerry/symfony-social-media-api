<?php

namespace App\User\Infrastructure\Repository;

use App\User\Domain\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Infrastructure\Mapper\UserMapperInterface;
use App\User\Infrastructure\Table\UsersTable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

class DbalUserRepository implements UserRepositoryInterface
{
  private readonly Connection $connection;
  private readonly UserMapperInterface $userMapper;

  public function __construct(Connection $connection, UserMapperInterface $userMapper)
  {
    $this->connection = $connection;
    $this->userMapper = $userMapper;
  }

  public function find(UuidInterface $id): ?User
  {
    $builder = new QueryBuilder($this->connection);
    $builder
      ->select('*')
      ->from(UsersTable::TABLE_NAME)
      ->where(UsersTable::ID . ' = ?')
      ->setParameter(1, $id->toString());
    $result = $builder->executeQuery();
    $data = $result->fetchAssociative();
    return false === $data ? null : $this->userMapper->toEntity($data);
  }

  /**
   * @throws \RuntimeException
   */
  public function getRandom(): User
  {
    $sql = 'SELECT * FROM ' . UsersTable::TABLE_NAME .
      ' OFFSET floor(random() * (SELECT count(*) FROM ' . UsersTable::TABLE_NAME . ')) LIMIT 1';
    $statement = $this->connection->prepare($sql);
    $result = $statement->executeQuery();
    $data = $result->fetchAssociative();
    if (false === $data) {
      throw new \RuntimeException('No users found in the database.');
    }
    return $this->userMapper->toEntity($data);
  }

  public function save(User $user): void
  {
    $builder = new QueryBuilder($this->connection);
    $builder
      ->insert(UsersTable::TABLE_NAME)
      ->values([
        UsersTable::ID => ':' . UsersTable::ID,
        UsersTable::USERNAME => ':' . UsersTable::USERNAME,
        UsersTable::EMAIL => ':' . UsersTable::EMAIL,
        UsersTable::AVATAR_URL => ':' . UsersTable::AVATAR_URL,
        UsersTable::CREATED_AT => ':' . UsersTable::CREATED_AT,
        UsersTable::UPDATED_AT => ':' . UsersTable::UPDATED_AT
      ])
      ->setParameters($this->userMapper->toArray($user));

    $builder->executeQuery();
  }

  public function delete(UuidInterface $userId): void
  {
    $builder = $this->connection->createQueryBuilder()
      ->delete(UsersTable::TABLE_NAME)
      ->where(UsersTable::ID . ' = ?')
      ->setParameter(1, $userId->toString());
    $builder->executeQuery();
  }

  public function update(User $user): void
  {
    $builder = $this->connection->createQueryBuilder()
      ->update(UsersTable::TABLE_NAME)
      ->set(UsersTable::USERNAME, ':' . UsersTable::USERNAME)
      ->set(UsersTable::EMAIL, ':' . UsersTable::EMAIL)
      ->set(UsersTable::AVATAR_URL, ':' . UsersTable::AVATAR_URL)
      ->set(UsersTable::UPDATED_AT, ':' . UsersTable::UPDATED_AT)
      ->where(UsersTable::ID . '=:' . UsersTable::ID)
      ->setParameters([
        UsersTable::USERNAME => $user->getUsername(),
        UsersTable::EMAIL => $user->getEmail(),
        UsersTable::AVATAR_URL => $user->getAvatarUrl(),
        UsersTable::UPDATED_AT => $user->getUpdatedAt()?->format('Y-m-d H:i:s.v'),
        UsersTable::ID => $user->getId()->toString(),
      ]);
    $builder->executeQuery();
  }
}
