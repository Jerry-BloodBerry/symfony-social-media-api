<?php

namespace App\Application\Post\Query\MessageHandler;

use App\Application\Post\Query\Message\PostsByUserIdQuery;
use App\Application\Service\CQRS\QueryHandlerInterface;
use App\Domain\Interface\PostRepositoryInterface;
use App\Domain\Post;

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
