<?php

namespace App\User\Application\Command\MessageHandler;

use App\User\Application\Command\Message\CreateUserMessage;
use App\Common\CQRS\CommandHandlerInterface;
use App\User\Domain\UserRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateUserMessageHandler implements CommandHandlerInterface
{
  public function __construct(
    private readonly UserRepositoryInterface $userRepository
  ) {
  }

  public function __invoke(CreateUserMessage $message): User
  {
    $user = User::create(
      $message->id,
      $message->username,
      $message->email,
      $message->avatarUrl,
      $message->createdAt
    );
    $this->userRepository->save($user);

    return $user;
  }
}
