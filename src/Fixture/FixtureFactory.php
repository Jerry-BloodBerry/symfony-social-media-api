<?php

namespace App\Fixture;

use Psr\Container\ContainerInterface;

class FixtureFactory implements FixtureFactoryInterface
{
  /**
   * @var array<string>
   */
  private array $registeredFixtures = [];
  private ContainerInterface $container;

  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
  }

  public function registerFixture(string $fixtureClass, int $count): void
  {
    if (!in_array(FixtureInterface::class, class_implements($fixtureClass))) {
      throw new \InvalidArgumentException(sprintf('Class %s does not implement FixtureInterface.', $fixtureClass));
    }

    $this->registeredFixtures[$fixtureClass] = $count;
  }

  public function load(): void
  {
    foreach ($this->registeredFixtures as $fixtureClass => $count) {
      $fixture = $this->container->get($fixtureClass);
      $fixture->load($count);
    }
  }
}
