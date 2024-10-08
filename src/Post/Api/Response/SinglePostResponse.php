<?php

namespace App\Post\Api\Response;

use App\Post\Domain\Post;
use OpenApi\Attributes as OA;

class SinglePostResponse
{

    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid')]
        public readonly string $id,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTime $updatedAt,
        public readonly string $content,
        public readonly AuthorResponse $author
    ) {
    }

    public static function from(Post $post): self
    {
        return new self(
            $post->getId()->toString(),
            $post->getCreatedAt(),
            $post->getUpdatedAt(),
            $post->getContent(),
            AuthorResponse::from($post->getAuthor())
        );
    }
}