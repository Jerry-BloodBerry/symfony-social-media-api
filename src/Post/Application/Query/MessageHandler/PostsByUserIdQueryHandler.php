<?php

namespace App\Post\Application\Query\MessageHandler;

use App\Post\Application\Query\Message\PostsByUserIdQuery;
use App\Common\CQRS\QueryHandlerInterface;
use App\Post\Domain\PostRepositoryInterface;
use App\Post\Domain\Post;

final class PostsByUserIdQueryHandler implements QueryHandlerInterface
{
  private PostRepositoryInterface $postRepository;

  public function __construct(PostRepositoryInterface $postRepository)
  {
    $this->postRepository = $postRepository;
  }

  /**
   * @return Post[]
   */
  public function __invoke(PostsByUserIdQuery $query): array
  {
    return $this->postRepository->getUserPosts($query->id, $query->limit, $query->offset);
  }

}
