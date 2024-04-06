<?php

declare(strict_types=1);

namespace App\User\Domain;

use Ramsey\Uuid\UuidInterface;

class User
{
  private UuidInterface $id;
  private string $username;
  private string $email;
  private \DateTime $createdAt;
  private ?\DateTime $updatedAt;
  private string $avatarUrl;

  private function __construct(
    UuidInterface $id,
    string $username,
    string $email,
    string $avatarUrl,
    \DateTime $createdAt,
    ?\DateTime $updatedAt
  ) {
    $this->id = $id;
    $this->username = $username;
    $this->email = $email;
    $this->avatarUrl = $avatarUrl;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
  }

  public static function create(
    UuidInterface $id,
    string $username,
    string $email,
    string $avatarUrl,
    \DateTime $createdAt
  ): self {
    return new self($id, $username, $email, $avatarUrl, $createdAt, null);
  }

  public static function restore(
    UuidInterface $id,
    string $username,
    string $email,
    string $avatarUrl,
    \DateTime $createdAt,
    ?\DateTime $updatedAt
  ): self {
    return new self($id, $username, $email, $avatarUrl, $createdAt, $updatedAt);
  }

  public function getId(): UuidInterface
  {
    return $this->id;
  }

  public function getUsername(): string
  {
    return $this->username;
  }

  public function setUsername(string $username): void
  {
    $this->username = $username;
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function getCreatedAt(): \DateTime
  {
    return $this->createdAt;
  }

  public function getUpdatedAt(): ?\DateTime
  {
    return $this->updatedAt;
  }

  public function setUpdatedAt(?\DateTime $updatedAt): void
  {
    $this->updatedAt = $updatedAt;
  }

  public function getAvatarUrl(): string
  {
    return $this->avatarUrl;
  }

  public function setAvatarUrl(string $avatarUrl): void
  {
    $this->avatarUrl = $avatarUrl;
  }
}
