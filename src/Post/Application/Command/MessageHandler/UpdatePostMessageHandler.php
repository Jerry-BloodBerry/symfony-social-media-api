<?php

namespace App\Post\Application\Command\MessageHandler;

use App\Common\CQRS\CommandHandlerInterface;
use App\Post\Application\Command\Message\UpdatePostMessage;
use App\Post\Domain\PostRepositoryInterface;
use App\Post\Exception\PostNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdatePostMessageHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository
    ) {
    }

    public function __invoke(UpdatePostMessage $message): void
    {
        $post = $this->postRepository->find($message->id);
        if ($post === null) {
            throw new PostNotFoundException($message->id);
        }
        $post->updateContent($message->content);
        $this->postRepository->save($post);
    }
}