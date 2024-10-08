<?php

namespace App\Fixture;

use App\Post\Domain\Author;
use App\Post\Domain\PostRepositoryInterface;
use App\User\Domain\UserRepositoryInterface;
use App\Post\Domain\Post;
use Ramsey\Uuid\Uuid;
use Faker\Factory;
use Faker\Generator;
use Ramsey\Uuid\UuidInterface;

class PostFixture implements FixtureInterface
{
  private readonly PostRepositoryInterface $postRepository;
  private readonly UserRepositoryInterface $userRepository;
  private readonly Generator $faker;

  public function __construct(PostRepositoryInterface $postRepository, UserRepositoryInterface $userRepository)
  {
    $this->postRepository = $postRepository;
    $this->userRepository = $userRepository;
    $this->faker = Factory::create();
    $this->faker->seed(1234);
  }

  public function load(int $count): void
  {
    $randomUser = $this->userRepository->getRandom();
    for ($i = 0; $i < $count; $i++) {
      $post = Post::create(
        $this->generateUuid(),
        \DateTimeImmutable::createFromMutable($this->generateDateTime()),
        new Author($randomUser->getId(), $randomUser->getUsername()),
        $this->generateContent()
      );

      $this->postRepository->save($post);
    }
  }

  private function generateUuid(): UuidInterface
  {
    return Uuid::uuid4();
  }

  private function generateDateTime(): \DateTime
  {
    return $this->faker->dateTimeBetween('-8 years', 'now', 'UTC');
  }

  private function generateContent(): string
  {
    return $this->faker->text(2500);
  }
}
