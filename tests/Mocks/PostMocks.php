<?php

namespace App\Tests\Mocks;

use App\Common\ClockInterface;
use App\Domain\Post;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class PostMocks
{
  private UuidInterface $id;
  private \DateTimeImmutable $createdAt;
  private string $content;
  private UuidInterface $authorId;

  public function __construct(ClockInterface $clock)
  {
    $this->id = Uuid::uuid4();
    $this->createdAt = $clock->utcNow();
    $this->content = 'Mock post content';
    $this->authorId = Uuid::uuid4();
  }

  public function withId(UuidInterface $id): self
  {
    $this->id = $id;
    return $this;
  }

  public function withCreatedAt(\DateTimeImmutable $createdAt): self
  {
    $this->createdAt = $createdAt;
    return $this;
  }

  public function withContent(string $content): self
  {
    $this->content = $content;
    return $this;
  }

  public function withAuthorId(UuidInterface $authorId): self
  {
    $this->authorId = $authorId;
    return $this;
  }

  public function build(): Post
  {
    return Post::create($this->id, $this->createdAt, $this->authorId, $this->content);
  }
}
