<?php

namespace App\Tests;

use App\Common\ClockInterface;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
class FixedClock implements ClockInterface
{
  private DateTimeImmutable $fixedDate;

  public function __construct(string $fixedDate)
  {
    $this->fixedDate = new DateTimeImmutable($fixedDate, new \DateTimeZone('UTC'));
  }

  public function utcNow(): DateTimeImmutable
  {
    return $this->fixedDate;
  }
}
