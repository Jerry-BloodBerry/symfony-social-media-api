<?php

namespace App\Application\Post\Query\MessageHandler;

use App\Application\Post\Query\Message\PostByIdQuery;
use App\Application\Service\CQRS\QueryHandlerInterface;
use App\Domain\Interface\PostRepositoryInterface;
use App\Domain\Post;

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
