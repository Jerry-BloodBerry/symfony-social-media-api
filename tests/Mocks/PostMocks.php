<?php

namespace App\Tests\Mocks;

use App\Common\ClockInterface;
use App\Post\Domain\Author;
use App\Post\Domain\Post;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class PostMocks
{
  private UuidInterface $id;
  private \DateTimeImmutable $createdAt;
  private string $content;
  private Author $author;

  public function __construct(ClockInterface $clock)
  {
    $this->id = Uuid::uuid4();
    $this->createdAt = $clock->utcNow();
    $this->content = 'Mock post content';
    $this->author = new Author(Uuid::uuid4(), 'Mock author');
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
    $this->author = new Author($authorId, $this->author->getName());
    return $this;
  }

  public function withAuthorName(string $name): self
  {
    $this->author = new Author($this->author->getId(), $name);
    return $this;
  }

  public function build(): Post
  {
    return Post::create($this->id, $this->createdAt, $this->author, $this->content);
  }
}
