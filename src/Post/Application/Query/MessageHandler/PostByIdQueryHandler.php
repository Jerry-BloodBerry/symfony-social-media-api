<?php

namespace App\Post\Application\Query\MessageHandler;

use App\Post\Application\Query\Message\PostByIdQuery;
use App\Common\CQRS\QueryHandlerInterface;
use App\Post\Domain\PostRepositoryInterface;
use App\Post\Domain\Post;

final class PostByIdQueryHandler implements QueryHandlerInterface
{
  private PostRepositoryInterface $postRepository;

  public function __construct(PostRepositoryInterface $postRepository)
  {
    $this->postRepository = $postRepository;
  }

  public function __invoke(PostByIdQuery $query): ?Post
  {
    return $this->postRepository->find($query->id);
  }

}
