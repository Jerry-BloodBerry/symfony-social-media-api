<?php

namespace App\User\Domain;

use App\User\Domain\User;
use Ramsey\Uuid\UuidInterface;

interface UserRepositoryInterface
{
  public function find(UuidInterface $id): ?User;
  public function getRandom(): User;
  public function save(User $user): void;

  public function delete(UuidInterface $userId): void;

  public function update(User $user): void;
}
