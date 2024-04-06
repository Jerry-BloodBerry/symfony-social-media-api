<?php

namespace App\User\Application\Query\MessageHandler;

use App\User\Application\Query\Message\UserByIdQuery;
use App\Common\CQRS\QueryHandlerInterface;
use App\User\Domain\UserRepositoryInterface;
use App\User\Domain\User;

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
