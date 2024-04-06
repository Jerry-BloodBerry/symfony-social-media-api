<?php

namespace App\Common\CQRS;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class QueryBus implements QueryBusInterface
{
  use HandleTrait {
    handle as handleQuery;
  }

  public function __construct(MessageBusInterface $queryBus)
  {
    $this->messageBus = $queryBus;
  }

  /** @return mixed */
  public function handle(QueryInterface $query)
  {
    return $this->handleQuery($query);
  }
}
