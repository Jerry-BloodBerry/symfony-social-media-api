<?php

namespace App\Application\Post\Query\MessageHandler;

use App\Application\Post\Query\Message\PostsQuery;
use App\Application\Service\CQRS\QueryHandlerInterface;
use App\Domain\Interface\PostRepositoryInterface;
use App\Domain\Post;

final class PostsQueryHandler implements QueryHandlerInterface
{
  private PostRepositoryInterface $postRepository;

  public function __construct(PostRepositoryInterface $postRepository)
  {
    $this->postRepository = $postRepository;
  }

  /**
   * @return Post[]
   */
  public function __invoke(PostsQuery $query): array
  {
    return $this->postRepository->findMany($query->limit, $query->offset);
  }

}
