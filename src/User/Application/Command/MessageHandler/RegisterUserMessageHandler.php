<?php

namespace App\User\Application\Command\MessageHandler;

use App\User\Application\Command\Message\RegisterUserMessage;
use App\Common\CQRS\CommandHandlerInterface;
use App\User\Domain\UserRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RegisterUserMessageHandler implements CommandHandlerInterface
{
  public function __construct(
    private readonly UserRepositoryInterface $userRepository
  ) {
  }

  public function __invoke(RegisterUserMessage $message): User
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
