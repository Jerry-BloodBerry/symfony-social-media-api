<?php

namespace App\Post\Application\Command\MessageHandler;

use App\Post\Application\Command\Message\CreatePostMessage;
use App\Common\CQRS\CommandHandlerInterface;
use App\Post\Domain\PostRepositoryInterface;
use App\Post\Domain\Post;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreatePostMessageHandler implements CommandHandlerInterface
{
  public function __construct(
    private readonly PostRepositoryInterface $postRepository
  ) {
  }

  public function __invoke(CreatePostMessage $message): void
  {
    $post = Post::create(
      $message->id,
      $message->createdAt,
      $message->authorId,
      $message->content
    );
    $this->postRepository->save($post);
  }
}
