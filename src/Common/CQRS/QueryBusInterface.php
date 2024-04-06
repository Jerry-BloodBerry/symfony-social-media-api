<?php

namespace App\Common\CQRS;

interface QueryBusInterface
{
  /** @return mixed */
  public function handle(QueryInterface $query);
}
