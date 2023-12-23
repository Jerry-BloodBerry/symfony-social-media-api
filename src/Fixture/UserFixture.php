<?php

namespace App\Fixture;

use App\Domain\Interface\UserRepositoryInterface;
use App\Domain\User;
use Ramsey\Uuid\Uuid;
use Faker\Factory;
use Faker\Generator;

class UserFixture implements FixtureInterface
{
  private UserRepositoryInterface $userRepository;
  private Generator $faker;
  public function __construct(UserRepositoryInterface $userRepository)
  {
    $this->userRepository = $userRepository;
    $this->faker = Factory::create();
  }
  public function load(int $count): void
  {
    for ($i = 0; $i < $count; $i++) {
      $user = User::create(
        Uuid::uuid4(),
        $this->faker->userName,
        $this->faker->email,
        $this->faker->imageUrl(),
        $this->faker->dateTime
      );
      $this->userRepository->save($user);
    }
  }
}
