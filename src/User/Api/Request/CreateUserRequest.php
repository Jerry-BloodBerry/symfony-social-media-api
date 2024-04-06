<?php

namespace App\User\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUserRequest
{
  public function __construct(
    #[Assert\NotBlank]
    #[Assert\Type("string")]
    public readonly string $username,
    #[Assert\NotBlank]
    #[Assert\Email]
    public readonly string $email
  ) {
  }
}
