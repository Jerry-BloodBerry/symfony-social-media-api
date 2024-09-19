<?php

namespace App\Post\Api\Request;

use OpenApi\Attributes as OA;
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
    #[OA\Property(type: 'string', format: 'uuid')]
    public readonly string $authorId
  ) {
  }
}
