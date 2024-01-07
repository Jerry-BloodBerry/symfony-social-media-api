<?php

namespace App\Infrastructure\Mapper\Post;

use App\Domain\Post;
use App\Infrastructure\Table\PostsTable;
use Ramsey\Uuid\Uuid;

class PostMapper implements PostMapperInterface
{
  public function toEntity(array $row): Post
  {
    return Post::restore(
      Uuid::fromString($row[PostsTable::ID]),
      new \DateTimeImmutable($row[PostsTable::CREATED_AT]),
      isset($row[PostsTable::UPDATED_AT]) ? new \DateTime($row[PostsTable::UPDATED_AT]) : null,
      Uuid::fromString($row[PostsTable::AUTHOR_ID]),
      $row[PostsTable::CONTENT]
    );
  }

  public function toArray(object $entity): array
  {
    if (!$entity instanceof Post) {
      throw new \InvalidArgumentException('Entity should be of type Post');
    }

    return [
      PostsTable::ID => $entity->getId()->toString(),
      PostsTable::AUTHOR_ID => $entity->getAuthorId()->toString(),
      PostsTable::CONTENT => $entity->getContent(),
      PostsTable::CREATED_AT => $entity->getCreatedAt()->format('Y-m-d H:i:s'),
      PostsTable::UPDATED_AT => $entity->getUpdatedAt() ? $entity->getUpdatedAt()->format('Y-m-d H:i:s') : null,
    ];
  }
}
