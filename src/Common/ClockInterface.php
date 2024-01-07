<?php

namespace App\Common;

interface ClockInterface
{
  public function utcNow(): \DateTimeImmutable;
}
