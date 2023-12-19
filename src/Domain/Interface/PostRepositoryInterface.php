<?php

namespace App\Domain\Interface;

use App\Domain\Post;
use Ramsey\Uuid\UuidInterface;

interface PostRepositoryInterface
{
  public function find(UuidInterface $id): ?Post;
  public function save(Post $post): void;

  public function delete(UuidInterface $postId): void;

  /**
   * @return array<Post>
   */
  public function getUserPosts(UuidInterface $userId, int $limit, int $offset): array;

  public function update(Post $post): void;
}
