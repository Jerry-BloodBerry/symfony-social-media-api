<?php

namespace App\Infrastructure\Repository;

use App\Domain\Interface\PostRepositoryInterface;
use App\Domain\Post;
use App\Infrastructure\Mapper\Post\PostMapperInterface;
use App\Infrastructure\Table\PostsTable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

class DbalPostRepository implements PostRepositoryInterface
{
  private readonly Connection $connection;
  private readonly PostMapperInterface $postMapper;

  public function __construct(Connection $connection, PostMapperInterface $postMapper)
  {
    $this->connection = $connection;
    $this->postMapper = $postMapper;
  }

  public function find(UuidInterface $id): ?Post
  {
    $builder = new QueryBuilder($this->connection);
    $builder
      ->select('*')
      ->from(PostsTable::TABLE_NAME)
      ->where(PostsTable::ID . ' = ?')
      ->setParameter(1, $id->toString());
    $result = $builder->executeQuery();
    $data = $result->fetchAssociative();
    return false === $data ? null : $this->postMapper->toEntity($data);
  }
  public function save(Post $post): void
  {
    $builder = new QueryBuilder($this->connection);
    $builder
      ->insert(PostsTable::TABLE_NAME)
      ->values([
        PostsTable::ID => ':' . PostsTable::ID,
        PostsTable::AUTHOR_ID => ':' . PostsTable::AUTHOR_ID,
        PostsTable::CONTENT => ':' . PostsTable::CONTENT,
        PostsTable::CREATED_AT => ':' . PostsTable::CREATED_AT,
        PostsTable::UPDATED_AT => ':' . PostsTable::UPDATED_AT
      ])
      ->setParameters($this->postMapper->toArray($post));

    $builder->executeQuery();
  }

  public function delete(UuidInterface $postId): void
  {
    $builder = $this->connection->createQueryBuilder()
      ->delete(PostsTable::TABLE_NAME)
      ->where(PostsTable::ID . ' = ?')
      ->setParameter(1, $postId->toString());
    $builder->executeQuery();
  }


  /**
   * @return array<Post>
   */
  public function getUserPosts(UuidInterface $userId, int $limit, int $offset): array
  {
    $builder = $this->connection->createQueryBuilder()
      ->select('*')
      ->from(PostsTable::TABLE_NAME)
      ->where(PostsTable::AUTHOR_ID . ' = ?')
      ->setParameter(1, $userId->toString());
    $result = $builder->executeQuery();
    $posts = [];
    foreach ($result->fetchAllAssociative() as $data) {
      $posts[] = $this->postMapper->toEntity($data);
    }
    return $posts;
  }

  public function update(Post $post): void
  {
    $builder = $this->connection->createQueryBuilder()
      ->update(PostsTable::TABLE_NAME)
      ->set(PostsTable::CONTENT, ':content')
      ->set(PostsTable::UPDATED_AT, ':updated_at')
      ->where(PostsTable::ID . '= :id')
      ->setParameters([
        PostsTable::CONTENT => $post->getContent(),
        PostsTable::UPDATED_AT => $post->getUpdatedAt()->format('Y-m-d H:i:s.v'),
        PostsTable::ID => $post->getId()->toString(),
      ]);
    $builder->executeQuery();
  }
}
