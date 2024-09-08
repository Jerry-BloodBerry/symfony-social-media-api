<?php

declare(strict_types=1);

namespace App\Post\Domain;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

class Post
{
  #[Groups(['post_details', 'post_list'])]
  private UuidInterface $id;
  #[Groups(['post_details'])]
  private \DateTimeImmutable $createdAt;
  #[Groups(['post_details'])]
  private ?\DateTime $updatedAt = null;
  #[Groups(['post_details', 'post_list'])]
  private string $content;
  #[Groups(['post_details', 'post_list'])]
  private UuidInterface $authorId;

  private function __construct(
    UuidInterface $id,
    \DateTimeImmutable $createdAt,
    ?\DateTime $updatedAt,
    UuidInterface $authorId,
    string $content
  ) {
    $this->id = $id;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
    $this->authorId = $authorId;
    $this->content = $content;
  }

  public static function create(
    UuidInterface $id,
    \DateTimeImmutable $createdAt,
    UuidInterface $authorId,
    string $content
  ): self {
    return new self($id, $createdAt, null, $authorId, $content);
  }

  public static function restore(
    UuidInterface $id,
    \DateTimeImmutable $createdAt,
    ?\DateTime $updatedAt,
    UuidInterface $authorId,
    string $content
  ): self {
    return new self($id, $createdAt, $updatedAt, $authorId, $content);
  }

  public function getId(): UuidInterface
  {
    return $this->id;
  }

  public function getCreatedAt(): \DateTimeImmutable
  {
    return $this->createdAt;
  }

  public function getUpdatedAt(): ?\DateTime
  {
    return $this->updatedAt;
  }

  private function updateTimestamp(): void
  {
    $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
  }

  public function getContent(): string
  {
    return $this->content;
  }

  public function updateContent(string $content): void
  {
    $this->content = $content;
    $this->updateTimestamp();
  }

  public function getAuthorId(): UuidInterface
  {
    return $this->authorId;
  }

}
