<?php

namespace App\Post\Application\Command\MessageHandler;

use App\Post\Application\Command\Message\CreatePostMessage;
use App\Common\CQRS\CommandHandlerInterface;
use App\Post\Application\Service\AuthorFinderInterface;
use App\Post\Domain\PostRepositoryInterface;
use App\Post\Domain\Post;
use App\Post\Exception\AuthorNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreatePostMessageHandler implements CommandHandlerInterface
{
  public function __construct(
    private readonly PostRepositoryInterface $postRepository,
    private readonly AuthorFinderInterface $authorFinder
  ) {
  }

  public function __invoke(CreatePostMessage $message): void
  {
    $author = $this->authorFinder->find($message->authorId);
    if ($author === null) {
      throw new AuthorNotFoundException($message->authorId);
    }
    $post = Post::create(
      $message->id,
      $message->createdAt,
      $author,
      $message->content
    );
    $this->postRepository->save($post);
  }
}
