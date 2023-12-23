<?php

namespace App\Fixture;

interface FixtureInterface
{
  public function load(int $count): void;
}
