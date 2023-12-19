<?php

namespace App\Application\Transformer;

use App\Domain\User;

class UserTransformer extends BaseTransformer
{

  /**
   * @return array{
   *  id: string,
   *  username: string,
   *  email: string,
   *  avatarUrl: string,
   *  createdAt: string,
   *  updatedAt: string
   * }
   */
  public function transform(User $user): array
  {
    return [
      'id' => $user->getId()->toString(),
      'username' => $user->getUsername(),
      'email' => $user->getEmail(),
      'avatarUrl' => $user->getAvatarUrl(),
      'createdAt' => $this->formatDateTime($user->getCreatedAt()),
      'updatedAt' => null !== $user->getUpdatedAt() ? $this->formatDateTime($user->getUpdatedAt()) : null,
    ];
  }
}
