<?php

namespace App\Application\Service\CQRS;

interface CommandBusInterface
{
  public function dispatch(CommandInterface $command): void;
}
