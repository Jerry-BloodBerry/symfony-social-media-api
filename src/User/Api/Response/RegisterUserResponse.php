<?php

namespace App\User\Api\Response;

use App\User\Domain\User;
use OpenApi\Attributes as OA;

class RegisterUserResponse
{

    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid')]
        public readonly string $id,
        public readonly string $username,
        #[OA\Property(type: 'string', format: 'email')]
        public readonly string $email,
        public readonly \DateTime $createdAt,
        public readonly ?\DateTime $updatedAt,
        public readonly string $avatarUrl,
    ) {
    }

    public static function fromUser(User $user): self
    {
        return new self(
            $user->getId()->toString(),
            $user->getUsername(),
            $user->getEmail(),
            $user->getCreatedAt(),
            $user->getUpdatedAt(),
            $user->getAvatarUrl()
        );
    }
}