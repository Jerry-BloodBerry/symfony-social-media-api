<?php

namespace App\Application\User\Command\MessageHandler;

use App\Application\User\Command\Message\CreateUserMessage;
use App\Application\Service\CQRS\CommandHandlerInterface;
use App\Domain\Interface\UserRepositoryInterface;
use App\Domain\User;
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
