<?php

namespace App\Post\Api\Response;

use App\Post\Domain\Post;
use OpenApi\Attributes as OA;


class ListPostResponse
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid')]
        public readonly string $id,
        public readonly string $content,
        #[OA\Property(type: 'string', format: 'uuid')]
        public readonly string $authorId
    ) {
    }

    public static function from(Post $post): self
    {
        return new self(
            $post->getId()->toString(),
            $post->getContent(),
            $post->getAuthorId()->toString()
        );
    }
}