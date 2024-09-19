<?php

namespace App\User\Api\Request;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserRequest
{
  public function __construct(
    #[Assert\NotBlank]
    #[Assert\Type("string")]
    public readonly string $username,
    #[Assert\NotBlank]
    #[Assert\Email]
    #[OA\Property(type: 'string', format: 'email')]
    public readonly string $email
  ) {
  }
}
