<?php

declare(strict_types=1);

namespace App\Post\Domain;

use App\Common\Entity;
use Ramsey\Uuid\UuidInterface;

class Post extends Entity
{
  private \DateTimeImmutable $createdAt;
  private ?\DateTime $updatedAt = null;
  private string $content;
  private Author $author;

  private function __construct(
    UuidInterface $id,
    \DateTimeImmutable $createdAt,
    ?\DateTime $updatedAt,
    Author $author,
    string $content
  ) {
    parent::__construct($id);
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
    $this->author = $author;
    $this->content = $content;
  }

  public static function create(
    UuidInterface $id,
    \DateTimeImmutable $createdAt,
    Author $author,
    string $content
  ): self {
    $post = new self($id, $createdAt, null, $author, $content);
    $post->raise(new PostCreatedDomainEvent($id));
    return $post;
  }

  public static function restore(
    UuidInterface $id,
    \DateTimeImmutable $createdAt,
    ?\DateTime $updatedAt,
    Author $author,
    string $content
  ): self {
    return new self($id, $createdAt, $updatedAt, $author, $content);
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

  public function update(string $content): void
  {
    $this->content = $content;
    $this->updateTimestamp();
    $this->raise(new PostUpdatedDomainEvent($this->id));
  }

  public function getAuthor(): Author
  {
    return $this->author;
  }

}
