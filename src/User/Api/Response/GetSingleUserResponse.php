<?php

namespace App\User\Api\Response;

use App\User\Domain\User;
use OpenApi\Attributes as OA;

class GetSingleUserResponse
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid')]
        public string $id,
        public string $username,
        #[OA\Property(type: 'string', format: 'email')]
        public string $email,
        public \DateTime $createdAt,
        public ?\DateTime $updatedAt,
        public ?string $avatarUrl
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