<?php

namespace App\User\Application\Command\MessageHandler;

use App\Common\CQRS\CommandHandlerInterface;
use App\User\Application\Command\Message\UpdateUserMessage;
use App\User\Domain\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Exception\UserNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateUserMessageHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function __invoke(UpdateUserMessage $message): User
    {
        $user = $this->userRepository->find($message->id);
        if ($user === null) {
            throw new UserNotFoundException($message->id);
        }
        $user->changeUsername($message->username);
        $user->changeEmail($message->email);
        $user->updateProfileInfo($message->avatarUrl);

        $this->userRepository->update($user);

        return $user;
    }
}
