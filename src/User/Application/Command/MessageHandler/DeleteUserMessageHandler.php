<?php

namespace App\User\Application\Command\MessageHandler;

use App\User\Application\Command\Message\DeleteUserMessage;
use App\Common\CQRS\CommandHandlerInterface;
use App\User\Domain\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteUserMessageHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function __invoke(DeleteUserMessage $message)
    {
        $this->userRepository->delete($message->id);
    }
}
