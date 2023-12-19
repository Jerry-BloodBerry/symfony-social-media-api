<?php

namespace App\Application\User\Query\MessageHandler;

use App\Application\User\Query\Message\UserByIdQuery;
use App\Application\Service\CQRS\QueryHandlerInterface;
use App\Domain\Interface\UserRepositoryInterface;
use App\Domain\User;

final class UserByIdQueryHandler implements QueryHandlerInterface
{
  private UserRepositoryInterface $userRepository;

  public function __construct(UserRepositoryInterface $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  public function __invoke(UserByIdQuery $query): ?User
  {
    return $this->userRepository->find($query->id);
  }

}
