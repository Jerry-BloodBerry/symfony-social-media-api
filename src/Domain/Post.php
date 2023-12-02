<?php

declare(strict_types=1);

namespace App\Domain;

use Ramsey\Uuid\Rfc4122\UuidV4;

class Post
{
  private UuidV4 $id;
  private \DateTime $createdAt;
  private ?\DateTime $updatedAt = null;
  private string $content;
  private UuidV4 $authorId;

  private function __construct(
    UuidV4 $id,
    \DateTime $createdAt,
    ?\DateTime $updatedAt,
    UuidV4 $authorId,
    string $content
  ) {
    $this->id = $id;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
    $this->authorId = $authorId;
    $this->content = $content;
  }

  public static function create(
    UuidV4 $id,
    \DateTime $createdAt,
    UuidV4 $authorId,
    string $content
  ): self {
    return new self($id, $createdAt, null, $authorId, $content);
  }

  public static function restore(
    UuidV4 $id,
    \DateTime $createdAt,
    ?\DateTime $updatedAt,
    UuidV4 $authorId,
    string $content
  ): self {
    return new self($id, $createdAt, $updatedAt, $authorId, $content);
  }

  public function getId(): UuidV4
  {
    return $this->id;
  }

  public function getCreatedAt(): \DateTime
  {
    return $this->createdAt;
  }

  public function getUpdatedAt(): ?\DateTime
  {
    return $this->updatedAt;
  }

  public function setUpdatedAt(\DateTime $updated_at): void
  {
    $this->updatedAt = $updated_at;
  }

  public function getContent(): string
  {
    return $this->content;
  }

  public function getAuthorId(): UuidV4
  {
    return $this->authorId;
  }


  public function equals(self $toCompare): bool
  {
    return $this->getId() == $toCompare->getId();
  }
}
