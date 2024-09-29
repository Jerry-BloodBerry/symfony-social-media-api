<?php

namespace App\Post\Api\Response;

use App\Post\Domain\Author;
use OpenApi\Attributes as OA;

class AuthorResponse
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid')]
        public readonly string $id,
        public readonly string $name
    ) {
    }

    public static function from(Author $author): self
    {
        return new self($author->getId()->toString(), $author->getName());
    }
}