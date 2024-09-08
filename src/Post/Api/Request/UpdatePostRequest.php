<?php

namespace App\Post\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;

class UpdatePostRequest
{
  public function __construct(
    #[Assert\NotBlank]
    #[Assert\Type("string")]
    public readonly string $content,
  ) {
  }
}
