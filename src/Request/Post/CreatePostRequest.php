<?php

namespace App\Request\Post;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Uuid;

class CreatePostRequest
{
  public function __construct(
  #[Assert\NotBlank]
  #[Assert\Type("string")]
    public readonly string $content,
  #[Assert\NotBlank]
  #[Assert\Uuid(versions: [Uuid::V4_RANDOM])]
    public readonly string $authorId
  ) {
  }
}
