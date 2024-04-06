<?php

declare(strict_types=1);

namespace App\PostComment\Domain;

use Ramsey\Uuid\Rfc4122\UuidV4;

class PostComment
{
  private UuidV4 $id;
  private UuidV4 $authorId;
  private \DateTime $createdAt;
  private ?\DateTime $updatedAt;
  private string $content;
  private ?UuidV4 $parentId;

  private function __construct(
    UuidV4 $id,
    UuidV4 $authorId,
    \DateTime $createdAt,
    ?\DateTime $updatedAt,
    string $content,
    ?UuidV4 $parentId
  ) {
    $this->id = $id;
    $this->authorId = $authorId;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
    $this->content = $content;
    $this->parentId = $parentId;
  }

  public static function create(
    UuidV4 $id,
    UuidV4 $authorId,
    \DateTime $createdAt,
    string $content,
    ?UuidV4 $respondsToPostCommment
  ): self {
    return new self($id, $authorId, $createdAt, null, $content, $respondsToPostCommment);
  }

  public static function restore(
    UuidV4 $id,
    UuidV4 $authorId,
    \DateTime $createdAt,
    ?\DateTime $updatedAt,
    string $content,
    ?UuidV4 $respondsToPostCommment
  ): self {
    return new self($id, $authorId, $createdAt, $updatedAt, $content, $respondsToPostCommment);
  }

  public function getId(): UuidV4
  {
    return $this->id;
  }

  public function getAuthorId(): UuidV4
  {
    return $this->authorId;
  }

  public function getCreatedAt(): \DateTime
  {
    return $this->createdAt;
  }

  public function getUpdatedAt(): ?\DateTime
  {
    return $this->updatedAt;
  }

  public function getContent(): string
  {
    return $this->content;
  }

  public function getParentId(): ?UuidV4
  {
    return $this->parentId;
  }
}
