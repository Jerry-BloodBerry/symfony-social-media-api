<?php

declare(strict_types=1);

namespace App\Like\Domain;

use Ramsey\Uuid\Rfc4122\UuidV4;

class Like
{
  private UuidV4 $authorId;
  private \DateTime $createdAt;

  private function __construct(UuidV4 $authorId, \DateTime $createdAt)
  {
    $this->authorId = $authorId;
    $this->createdAt = $createdAt;
  }

  public static function create(UuidV4 $authorId, \DateTime $createdAt): self
  {
    return new self(
      $authorId,
      $createdAt
    );
  }

  public static function restore(UuidV4 $authorId, \DateTime $createdAt): self
  {
    return new self(
      $authorId,
      $createdAt
    );
  }

  public function getAuthorId(): UuidV4
  {
    return $this->authorId;
  }

  public function getCreatedAt(): \DateTime
  {
    return $this->createdAt;
  }
}
