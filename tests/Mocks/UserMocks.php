<?php

namespace App\Tests\Mocks;

use App\User\Domain\User;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UserMocks
{
  private UuidInterface $id;
  private string $username;
  private string $email;
  private string $avatarUrl;
  private \DateTime $createdAt;

  public function __construct()
  {
    $this->id = Uuid::uuid4();
    $this->username = 'mock_user';
    $this->email = 'mock_user@example.com';
    $this->avatarUrl = 'https://example.com/mock_user.png';
    $this->createdAt = new \DateTime();
  }

  public function withId(UuidInterface $id): self
  {
    $this->id = $id;
    return $this;
  }

  public function withUsername(string $username): self
  {
    $this->username = $username;
    return $this;
  }

  public function withEmail(string $email): self
  {
    $this->email = $email;
    return $this;
  }

  public function withAvatarUrl(string $avatarUrl): self
  {
    $this->avatarUrl = $avatarUrl;
    return $this;
  }

  public function withCreatedAt(\DateTime $createdAt): self
  {
    $this->createdAt = $createdAt;
    return $this;
  }

  public function build(): User
  {
    return User::create($this->id, $this->username, $this->email, $this->avatarUrl, $this->createdAt);
  }
}
