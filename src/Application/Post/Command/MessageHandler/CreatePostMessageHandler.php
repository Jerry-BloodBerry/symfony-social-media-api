<?php

namespace App\Application\Post\Command\MessageHandler;

use App\Application\Post\Command\Message\CreatePostMessage;
use App\Application\Service\CQRS\CommandHandlerInterface;
use App\Domain\Interface\PostRepositoryInterface;
use App\Domain\Post;

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
