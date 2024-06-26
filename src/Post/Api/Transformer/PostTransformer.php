<?php

namespace App\Post\Api\Transformer;

use App\Common\BaseTransformer;
use App\Post\Domain\Post;

class PostTransformer extends BaseTransformer
{

  /**
   * @return array{
   *  id: string,
   *  authorId: string,
   *  content: string,
   *  createdAt: string,
   *  updatedAt: string
   * }
   */
  public function transform(Post $post): array
  {
    return [
      'id' => $post->getId()->toString(),
      'authorId' => $post->getAuthorId()->toString(),
      'content' => $post->getContent(),
      'createdAt' => $this->formatDateTime($post->getCreatedAt()),
      'updatedAt' => null !== $post->getUpdatedAt() ? $this->formatDateTime($post->getUpdatedAt()) : null,
    ];
  }
}
