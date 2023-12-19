<?php

namespace App\Application\Service\CQRS;

interface QueryBusInterface
{
  /** @return mixed */
  public function handle(QueryInterface $query);
}
