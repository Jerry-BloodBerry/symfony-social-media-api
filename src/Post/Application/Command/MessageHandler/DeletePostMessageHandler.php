<?php

namespace App\Post\Application\Command\MessageHandler;

use App\Common\CQRS\CommandHandlerInterface;
use App\Post\Application\Command\Message\DeletePostMessage;
use App\Post\Domain\PostRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeletePostMessageHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository
    ) {
    }

    public function __invoke(DeletePostMessage $message): void
    {
        $this->postRepository->delete($message->id);
    }
}