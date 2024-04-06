<?php

namespace App\Post\Application\Query\MessageHandler;

use App\Post\Application\Query\Message\PostsQuery;
use App\Common\CQRS\QueryHandlerInterface;
use App\Post\Domain\PostRepositoryInterface;
use App\Post\Domain\Post;

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
