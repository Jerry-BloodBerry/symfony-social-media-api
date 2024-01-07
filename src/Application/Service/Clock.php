<?php

namespace App\Application\Service;

use App\Common\ClockInterface;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'dev')]
#[When(env: 'prod')]
class Clock implements ClockInterface
{
  public function utcNow(): DateTimeImmutable
  {
    return new DateTimeImmutable('now', new \DateTimeZone('UTC'));
  }
}
